<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classDbHandler\bookData\BookAuthorDBHandler;
use classDbHandler\bookData\BookCoverDBHandler;
use classDbHandler\bookData\BookDiscountDBHandler;
use classDbHandler\bookData\BookPriceDBHandler;
use classDbHandler\bookData\BookPrimaryDataDBHAndler;
use classDbHandler\metaData\MetaLanguageDBHandler;
use classModel\RequestParameters;
use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;
use helper\ImgHelper;

class BookDataGetter
{
    public function getBookPrimaryData(RequestParameters $parameters )
    {
//        $bookDataGetter = new BookDataDBHAndler();
        $isbn = $parameters->getUrlParameters()[0];
        $result = (new BookPrimaryDataDBHAndler())->getByIsbn($isbn);
        $result['author'] = $this->getBookAuthor($isbn);
        $result['price'] = (new BookPriceDBHandler())->getPriceByIsbn($isbn);
        $result['discount'] = (new BookDiscountDBHandler())->getQuantityByIsbn($isbn);
        $result['cover_thumbnail'] = $this->getCoverThumbnail($isbn);

//        $result = $bookDataGetter->getPrimaryData();
        throw new HttpResponseTriggerException(true, $result);
    }

    private function getBookAuthor($isbn): array
    {
        $authorIDs= (new BookAuthorDBHandler())->getByIsbn($isbn);
        $authorGetter = new AuthorDBHandler();
        $result =[];
        foreach ($authorIDs as $value)
        {
            $result[$value]= $authorGetter->getNameByID($value);
        }
        asort($result);
        return $result;
    }

    private function getCoverThumbnail(mixed $isbn)
    {
        $coverhandler = new BookCoverDBHandler();
        $coverData = $coverhandler->getByIsbn($isbn);
        if ($coverData === false) {
            return ImgHelper::convertImageToBase64String( 'image\coverThumbnail\no_cover.jpg');
        }
        else
        {
            if ($coverData['has_cover'] === '1' && $coverData['has_thumbnail'] === '0') {
                ImgHelper::createThumbnail($isbn . '.' . $coverData['extension'], 'image\cover\\', ROOT . 'image\coverThumbnail\\', 150, 212);
                $coverhandler->setHasThumbnailToTrueByIsbn($isbn);
            }
            return ImgHelper::convertImageToBase64String( 'image\coverThumbnail\\' . $isbn . '.' . $coverData['extension']);
        }
    }

}
