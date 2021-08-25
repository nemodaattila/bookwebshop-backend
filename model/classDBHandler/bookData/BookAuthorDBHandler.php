<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class BookAuthorDBHandler database connection/functions to table book_author
 * @package classDbHandler\bookData
 */
class BookAuthorDBHandler extends DBHandlerParent
{

    /**
     * gets the author(s) of a book by isbn
     * @param string $isbn isbn number of a book
     * @return array author(s) of the book
     * @throws HttpResponseTriggerException on mysql query error
     */
    public function getByIsbn(string $isbn): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT author_id FROM book_author where isbn = ?');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[] = $value['author_id'];
        }
        return $result;
    }

    public function insert(string $isbn, int $authorId)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_author (isbn,author_id) VALUES (?,?)');
        $this->PDOLink->setValues([$isbn, $authorId]);
        return $this->PDOLink->execute();
    }
}
