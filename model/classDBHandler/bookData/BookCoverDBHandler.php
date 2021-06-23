<?php

namespace classDbHandler\bookData;

use database\PDOProcessorBuilder;

class BookCoverDBHandler
{
    public function getByIsbn(string $isbn)
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bc.extension, bc.has_cover, bc.has_thumbnail FROM book_cover as bc WHERE book_isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        return $PDOLink->execute();
    }

    public function setHasThumbnailToTrueByIsbn(int $isbn)
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('UPDATE', true);
        $PDOLink->setCommand("UPDATE book_cover SET has_thumbnail = '1' WHERE book_isbn=?");
        $PDOLink->setValues($isbn);
        $PDOLink->execute();
    }
}
