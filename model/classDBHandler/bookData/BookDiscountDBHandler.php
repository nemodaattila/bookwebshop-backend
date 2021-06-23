<?php

namespace classDbHandler\bookData;

use database\PDOProcessorBuilder;

class BookDiscountDBHandler
{
    public function getQuantityByIsbn(string $isbn): int
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bd.discount_value FROM book_discount as bd WHERE isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        if ($tempResult === false) {
            return 0;
        }
        return $tempResult['discount_value'];
    }
}
