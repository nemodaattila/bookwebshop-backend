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
use classDbHandler\bookData\BookUploadDateDBHandler;
use classModel\Book;
use database\PDOConnection;
use exception\HttpResponseTriggerException;

class BookUploader
{

    public function addNewBook(array $data): array
    {
        $book = new Book($data);
        $book->checkNulls();
        $book->formatBeforeSave();

        $pdoConn = PDOConnection::getInstance();

        $exists = (new BookDBHAndler)->getByIsbn($book->getIsbn());
        if ($exists)
            throw new HttpResponseTriggerException(false, ['errorCode' => 'BUISBNAE']);
        $pdoConn->beginTransaction();
        (new BookDBHAndler)->insert(...$book->getPropertiesForBookTableInsert());
        array_map(function ($value) use ($book) {
            (new BookAuthorDBHandler())->insert($book->getIsbn(), $value);
        }, $book->getAuthorId());
        (new BookDescriptionDBHandler())->insert(...$book->getPropertiesForBookDescriptionTable());

        array_map(function ($value) use ($book) {
            (new BookTagDBHandler())->insert($book->getIsbn(), $value);
        }, $book->getTagId());
        (new BookPriceDBHandler())->insert($book->getIsbn(), $book->getPrice());
        if (!is_null($book->getSeriesId())) {
            (new BookSeriesDBHandler())->insert($book->getIsbn(), $book->getSeriesId());
        }
        if ($book->getDiscountType() !== 0) {
            (new BookDiscountDBHandler())->insert(...$book->getPropertiesForDiscountTable());
        }
        (new BookUploadDateDBHandler())->insert($book->getIsbn(), time());

        if (!is_null($book->getCoverUrl())) {
            $ext = pathinfo($book->getCoverUrl(), PATHINFO_EXTENSION);
            file_put_contents('image/cover/' . $book->getIsbn() . '.' . $ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);
        } elseif (!is_null($book->getCoverFile())) {
            $ext = explode('/', mime_content_type($book->getCoverFile()))[1];
            file_put_contents('image/cover/' . $book->getIsbn() . '.' . $ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);

        }
        $pdoConn->commit();
        return get_object_vars($book);
    }

}
