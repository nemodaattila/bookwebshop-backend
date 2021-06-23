<?php

namespace classDbHandler\bookData;

use database\PDOProcessorBuilder;

class BookPriceDBHandler
{
    public function getPriceByIsbn(string $isbn): int|bool
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bp.price FROM book_price as bp WHERE isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        if ($tempResult === null) {
            throw new RequestResultException(500, ['errorCode' => 'GBPISBNNE']);
        }
        return $tempResult['price'];
    }
}
