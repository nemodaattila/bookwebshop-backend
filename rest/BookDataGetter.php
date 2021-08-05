<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classDbHandler\bookData\BookAuthorDBHandler;
use classDbHandler\bookData\BookCoverDBHandler;
use classDbHandler\bookData\BookDescriptionDBHandler;
use classDbHandler\bookData\BookDiscountDBHandler;
use classDbHandler\bookData\BookPriceDBHandler;
use classDbHandler\bookData\BookPrimaryDataDBHAndler;
use classDbHandler\bookData\BookSeriesDBHandler;
use classDbHandler\bookData\BookTagDBHandler;
use classDbHandler\DiscountDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;
use helper\ImgHelper;

/**
 *
 * Class BookDataGetter http request processor, handles primary data of a book
 * primary data is: isbn, title, author(s), category_id, type_id, price, discount, cover_thumbnail (as bas64 string)
 * @package rest
 */
class BookDataGetter
{
    /**
     * collect primary data by calling multiple db handler functions
     * @param RequestParameters $parameters parameters from http request, url parameter used only (isbn)
     * @throws HttpResponseTriggerException the right result is thrown here, instead of a return
     */
    public function getBookPrimaryData(RequestParameters $parameters)
    {
        $isbn = $parameters->getUrlParameters()[0];
        $result = (new BookPrimaryDataDBHAndler())->getByIsbn($isbn);
        $result['author'] = $this->getBookAuthor($isbn);
        $result['price'] = (new BookPriceDBHandler())->getPriceByIsbn($isbn);
        $result['discount'] = (new BookDiscountDBHandler())->getQuantityByIsbn($isbn);
        $result['cover_thumbnail'] = $this->getCoverThumbnail($isbn);
        throw new HttpResponseTriggerException(true, $result);
    }

    /**
     * return the author(s) of a book
     * @param string $isbn isbn of a book
     * @return array author(s) of a book
     * @throws HttpResponseTriggerException
     */
    private function getBookAuthor(string $isbn): array
    {
        $authorIDs = (new BookAuthorDBHandler())->getByIsbn($isbn);
        $authorGetter = new AuthorDBHandler();
        $result = [];
        foreach ($authorIDs as $value) {
            $result[$value] = $authorGetter->getNameByID($value);
        }
        asort($result);
        return $result;
    }

    /**
     * checks if a book has cover if it has returns the base64 string of the cover's thumbnail,
     * if not returns a default thumbnail string
     * @param string $isbn isbn of a book
     * @return string cover thumbnail converted to base64 string
     * @throws HttpResponseTriggerException
     */
    private function getCoverThumbnail(string $isbn): string
    {
        $coverHandler = new BookCoverDBHandler();
        $coverData = $coverHandler->getByIsbn($isbn);
        if ($coverData == false) {
            return ImgHelper::convertImageToBase64String('image\coverThumbnail\no_cover.jpg');
        } else {
            if ($coverData['has_cover'] === '1' && $coverData['has_thumbnail'] === '0') {
                ImgHelper::createThumbnail($isbn . '.' . $coverData['extension'], 'image\cover\\', ROOT . 'image\coverThumbnail\\', 150, 212);
                $coverHandler->setHasThumbnailToTrueByIsbn($isbn);
            }
            return ImgHelper::convertImageToBase64String('image\coverThumbnail\\' . $isbn . '.' . $coverData['extension']);
        }
    }

    /**
     * returns the cover of a book in base 64 string form based in isbn
     * @param string $isbn
     * @return string
     * @throws HttpResponseTriggerException image conversion error
     */
    private function getCover(string $isbn): string
    {
        $coverHandler = new BookCoverDBHandler();
        $coverData = $coverHandler->getByIsbn($isbn);
        if ($coverData == false) {
            return ImgHelper::convertImageToBase64String('image\cover\no_cover.jpg');
        } else {
            return ImgHelper::convertImageToBase64String('image\cover\\' . $isbn . '.' . $coverData['extension']);
        }
    }

    /**
     * returns the secondary data of a book based on isbn
     * @param RequestParameters $parameters
     * @throws HttpResponseTriggerException
     */
    public function getBookSecondaryData(RequestParameters $parameters)
    {
        $isbn = $parameters->getUrlParameters()[0];
        $result = (new BookDescriptionDBHandler())->getByIsbn($isbn);
        $result['publisher'] = (new PublisherDBHandler())->getPublisherNameById($result['publisher_id']);
        unset($result['publisher_id']);
        $result['cover'] = $this->getCover($isbn);
        $disc = (new BookDiscountDBHandler())->getTypeByIsbn($isbn);
        $result['discount_type'] = [$disc => (new DiscountDBHandler())->getNameById($disc)];
        $serId = (new BookSeriesDBHandler())->getSeriesIdByIsbn($isbn);
        $result['series'] = ($serId === null) ? null : (new SeriesDBHandler())->getSeriesNameById($serId);
        $result['tags'] = (new BookTagDBHandler())->getTagsByIsbn($isbn);
        throw new HttpResponseTriggerException(true, $result);
    }

}
