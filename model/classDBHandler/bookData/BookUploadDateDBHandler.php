<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;

class BookUploadDateDBHandler extends DBHandlerParent
{
    public function insert(string $isbn, int $timestamp)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_upload_date (isbn,upload_date) VALUES (?,?)');
        $this->PDOLink->setValues([$isbn, $timestamp]);
        return $this->PDOLink->execute();
    }
}
