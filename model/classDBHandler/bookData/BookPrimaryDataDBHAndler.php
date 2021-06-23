<?php

namespace classDbHandler\bookData;

use database\PDOProcessorBuilder;

class BookPrimaryDataDBHAndler
{
    public function getByIsbn(string $isbn): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT isbn, title, type_id, category_id FROM book WHERE isbn=?");
        $PDOLink->setValues($isbn);
        $PDOLink->setFetchType('fetch');
        $tempResult = $PDOLink->execute();
        if ($tempResult === null) {
            throw new RequestResultException(500, ['errorCode' => 'GBPDISBNNE']);
        }
        return $tempResult;
    }
}
