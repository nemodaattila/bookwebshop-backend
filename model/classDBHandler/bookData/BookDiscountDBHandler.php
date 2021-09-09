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

    /**
     * return a type id based on isbn
     * @param string $isbn
     * @return int
     * @throws HttpResponseTriggerException
     */
    public function getTypeByIsbn(string $isbn): int
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT bd.discount_id FROM book_discount as bd WHERE isbn=?');
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            return 0;
        }
        return $tempResult['discount_id'];
    }

    public function insert(string $isbn, int $discountId, int $discountValue)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_discount (isbn,discount_id, discount_value) VALUES (?,?,?)');
        $this->PDOLink->setValues([$isbn, $discountId, $discountValue]);
        return $this->PDOLink->execute();
    }

    public function updateDiscountType(int $discountType, string $isbn)
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand('UPDATE book_discount SET discount_id = ? WHERE isbn = ?');
        $this->PDOLink->setValues([$discountType, $isbn]);
        return $this->PDOLink->execute();
    }

    public function updateDiscountValue(int $discountValue, string $isbn)
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand('UPDATE book_discount SET discount_value = ? WHERE isbn = ?');
        $this->PDOLink->setValues([$discountValue, $isbn]);
        return $this->PDOLink->execute();
    }

    public function delete(string $isbn)
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand('DELETE FROM book_discount WHERE isbn = ? ');
        $this->PDOLink->setValues([$isbn]);
        return $this->PDOLink->execute();

    }
}
