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
     * @return array|bool cover data
     * @throws HttpResponseTriggerException sql query error
     */
    public function getByIsbn(string $isbn): array|bool
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT bc.extension, bc.has_thumbnail FROM book_cover as bc WHERE book_isbn=?');
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($isbn);
        return $this->PDOLink->execute();
    }

    /**
     * sets the book_cover table's has_thumbnail attribute to 1 by isbn
     * @param string $isbn isbn of the book
     * @throws HttpResponseTriggerException sql query error
     */
    public function setHasThumbnailToTrueByIsbn(string $isbn): void
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand('UPDATE book_cover SET has_thumbnail = 1 WHERE book_isbn=?');
        $this->PDOLink->setValues($isbn);
        $this->PDOLink->execute();
    }

    public function insert(string $isbn, string $extension)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_cover (book_isbn,extension,has_thumbnail) VALUES (?,?,?)');
        $this->PDOLink->setValues([$isbn, $extension, 0]);
        return $this->PDOLink->execute();
    }

    public function delete(string $isbn)
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand('DELETE FROM book_cover WHERE book_isbn = ? ');
        $this->PDOLink->setValues([$isbn]);
        return $this->PDOLink->execute();
    }

}
