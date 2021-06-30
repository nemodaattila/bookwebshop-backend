<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class BookCoverDBHandler database connection/functions to book_cover
 * @package classDbHandler\bookData
 */
class BookCoverDBHandler extends DBHandlerParent
{
    /**
     * return cover parameters for a book by isbn
     * @param string $isbn isbn of a book
     * @return array cover data
     * @throws HttpResponseTriggerException wrong fetch type
     * @throws HttpResponseTriggerException sql query error
     */
    public function getByIsbn(string $isbn): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("SELECT bc.extension, bc.has_cover, bc.has_thumbnail FROM book_cover as bc WHERE book_isbn=?");
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($isbn);
        return $this->PDOLink->execute();
    }

    /**
     * sets the book_cover table's has_thumbnail attribute to 1 by isbn
     * @param int $isbn isbn of the book
     * @throws HttpResponseTriggerException wrong processor type
     * @throws HttpResponseTriggerException sql query error
     */
    public function setHasThumbnailToTrueByIsbn(int $isbn): void
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand("UPDATE book_cover SET has_thumbnail = '1' WHERE book_isbn=?");
        $this->PDOLink->setValues($isbn);
        $this->PDOLink->execute();
    }
}
