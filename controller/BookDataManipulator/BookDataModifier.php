<?php

namespace bookDataManipulator;

use classDbHandler\bookData\BookAuthorDBHandler;
use classDbHandler\bookData\BookCoverDBHandler;
use classDbHandler\bookData\BookDBHAndler;
use classDbHandler\bookData\BookDescriptionDBHandler;
use classDbHandler\bookData\BookDiscountDBHandler;
use classDbHandler\bookData\BookPriceDBHandler;
use classDbHandler\bookData\BookSeriesDBHandler;
use classDbHandler\bookData\BookTagDBHandler;
use classModel\Book;
use classModel\BookForModify;
use database\PDOConnection;
use exception\HttpResponseTriggerException;

class BookDataModifier
{
    public function modifyBookData(array $newData): array
    {
        $originalISBN = $newData['originalIsbn'];
        $originalCoverDelete = $newData['originalCoverDelete'];
        if (!isset($newData['isbn']))
            $newData['isbn'] = $originalISBN;
        unset($newData['originalIsbn']);
        unset ($newData['originalCoverDelete']);

        $book = new Book($newData);
//        $book->checkNulls();
        $book->formatBeforeSave();
        if (!(new BookDBHAndler)->getByIsbn($originalISBN))
            throw new HttpResponseTriggerException(false, ['errorCode' => 'BMOISBNNE']);
        if (isset($newData['isbn']) && ($newData['isbn'] !== $originalISBN)) {
            if ((new BookDBHAndler)->getByIsbn($newData['isbn']))
                throw new HttpResponseTriggerException(false, ['errorCode' => 'BMNISBNAE']);
        }
        $pdoConn = PDOConnection::getInstance();
        $pdoConn->beginTransaction();
        try {

            if ($book->getIsbn() !== $originalISBN) {
                (new BookDBHAndler)->updateIsbn($originalISBN, $book->getIsbn());
                $hasCover = (new BookCoverDBHandler())->getByIsbn($book->getIsbn());
                if ($hasCover) {
                    rename('image/cover/' . $originalISBN . '.' . $hasCover['extension'], 'image/cover/' . $book->getIsbn() . '.' . $hasCover['extension']);
                    rename('image/coverThumbnail/' . $originalISBN . '.' . $hasCover['extension'], 'image/coverThumbnail/' . $book->getIsbn() . '.' . $hasCover['extension']);
                }
            }//DO cover rename if necessery
            (new BookDBHAndler)->update($book->getPropertiesForBookTableUpdateWithoutIsbn(), $book->getIsbn());//author
            if (!empty($book->getAuthorId())) {
                $badbh = new BookAuthorDBHandler();
                foreach ($book->getAuthorId()[0] as $value)
                    $badbh->delete($book->getIsbn(), $value);
                foreach ($book->getAuthorId()[1] as $value)
                    $badbh->insert($book->getIsbn(), $value);
                unset($badbh);
            }
            (new BookDescriptionDBHandler())->update($book->getPropertiesForBookDescriptionTableUpdate(), $book->getIsbn());
            if (!empty($book->getTagId())) {
                $btdbh = new BookTagDBHandler();
                foreach ($book->getTagId()[0] as $value)
                    $btdbh->delete($book->getIsbn(), $value);
                foreach ($book->getTagId()[1] as $value)
                    $btdbh->insert($book->getIsbn(), $value);
                unset($btdbh);
            }
            if (!empty($book->getPrice()) || ($book->getPrice() === 0)) {
                (new BookPriceDBHandler())->update($book->getPrice(), $book->getIsbn());
            }
            if (!empty($book->getSeriesId())) {
                if ((new BookSeriesDBHandler())->getSeriesIdByIsbn($book->getIsbn()) === null) {
                    (new BookSeriesDBHandler())->insert($book->getIsbn(), $book->getSeriesId());
                } else
                    (new BookSeriesDBHandler())->update($book->getSeriesId(), $book->getIsbn());
            }
            if ($book->getSeries() === '') {
                (new BookSeriesDBHandler())->delete($book->getIsbn());
            }

//            print_r($book);
            if ($book->getDiscountType() > 0) {
                if ((new BookDiscountDBHandler())->getTypeByIsbn($book->getIsbn()) === 0) {
                    (new BookDiscountDBHandler())->insert(...$book->getPropertiesForDiscountTableInsert());
                } else {
                    (new BookDiscountDBHandler())->updateDiscountType($book->getDiscountType(), $book->getIsbn());
                }
                if ($book->getDiscount() > 0)
                    (new BookDiscountDBHandler())->updateDiscountValue($book->getDiscount(), $book->getIsbn());
            }
            if ($book->getDiscountType() === 0) {
                (new BookDiscountDBHandler())->delete($book->getIsbn());
            }

            if ($book->getCoverFileSource()) {
                $hasCover = (new BookCoverDBHandler())->getByIsbn($book->getIsbn());
                if (!$hasCover) {
                    $this->checkAndSaveCover($book);
                } else {
                    unlink('image/cover/' . $book->getIsbn() . '.' . $hasCover['extension']);
                    unlink('image/coverThumbnail/' . $book->getIsbn() . '.' . $hasCover['extension']);
                    (new BookCoverDBHandler())->delete($book->getIsbn());
                    $ext = $this->checkAndSaveCover($book);
                }
            } else {
                if ($originalCoverDelete) {
                    $cd = (new BookCoverDBHandler())->getByIsbn($book->getIsbn());
                    if ($cd) {
                        (new BookCoverDBHandler())->delete($book->getIsbn());
                        unlink('image/cover/' . $book->getIsbn() . '.' . $cd['extension']);
                        unlink('image/coverThumbnail/' . $book->getIsbn() . '.' . $cd['extension']);
                    }
                }
            }
        } catch (\Throwable $e) {
            $pdoConn->rollBack();
            throw $e;
        }

        $pdoConn->commit();
        return ['isbn' => $book->getIsbn() ?? $originalISBN];
    }

    /**
     * @param Book $book
     */
    public function checkAndSaveCover(Book $book): void
    {
        $ext = null;
        if (!is_null($book->getCoverUrl())) {
            $ext = pathinfo($book->getCoverUrl(), PATHINFO_EXTENSION);
            file_put_contents('image/cover/' . $book->getIsbn() . '.' . $ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);
        } elseif (!is_null($book->getCoverFile())) {
            $ext = explode('/', mime_content_type($book->getCoverFile()))[1];
            file_put_contents('image/cover/' . $book->getIsbn() . '.' . $ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);

        }
    }

}
