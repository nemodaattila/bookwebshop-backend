<?php

namespace classDbHandler\bookData;

use database\PDOProcessorBuilder;

class BookAuthorDBHandler
{
    public function getByIsbn(string $isbn): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT author_id FROM book_author where isbn = ?");
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[] = $value['author_id'];
        }
        return $result;
    }
}
