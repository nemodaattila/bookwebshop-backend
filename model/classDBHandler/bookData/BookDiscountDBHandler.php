<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class BookDiscountDBHandler database connection/functions to book_discount table
 * @package classDbHandler\bookData
 */
class BookDiscountDBHandler extends DBHandlerParent
{
    /**
     * returns the discount value of a book by isbn
     * @param string $isbn isbn of a book
     * @return int value of discount
     * @throws HttpResponseTriggerException wrong processor type
     * @throws HttpResponseTriggerException wrong fetch type
     * @throws HttpResponseTriggerException sql query error
     *
     */
    public function getQuantityByIsbn(string $isbn): int
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT bd.discount_value FROM book_discount as bd WHERE isbn=?');
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            return 0;
        }
        return $tempResult['discount_value'];
    }
}
