<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class BookPriceDBHandler database connection/ functions to table book_price
 * @package classDbHandler\bookData
 */
class BookPriceDBHandler extends DBHandlerParent
{
    /**
     * returns the price of a book
     * @param string $isbn isbn of a book
     * @return int price of the book (if no error)
     * @throws HttpResponseTriggerException wrong processor type
     * @throws HttpResponseTriggerException wrong fetch type
     * @throws HttpResponseTriggerException sql query error
     * @throws HttpResponseTriggerException if result is null (price not exists for book)
     */
    public function getPriceByIsbn(string $isbn): int
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT bp.price FROM book_price as bp WHERE isbn=?');
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === null) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'GBPISBNNE'], 500);
        }
        return $tempResult['price'];
    }
}
