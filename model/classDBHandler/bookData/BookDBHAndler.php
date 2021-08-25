<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class BookPrimaryDataDBHAndler database connection/functions to table book
 * @package classDbHandler\bookData
 */
class BookDBHAndler extends DBHandlerParent
{
    /**
     * return data from table book: isbn, title, type_id, category_id by isbn
     * @param string $isbn isbn of a book
     * @return array data in array
     * @throws HttpResponseTriggerException wrong processor type
     * @throws HttpResponseTriggerException wrong fetch type
     * @throws HttpResponseTriggerException sql query error
     */
    public function getByIsbn(string $isbn): array|bool
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT isbn, title, type_id, category_id FROM book WHERE isbn=?');
        $this->PDOLink->setValues($isbn);
        $this->PDOLink->setFetchType('fetch');
        return $this->PDOLink->execute();
    }

    public function insert(string $isbn, string $title, int $typeId, int $categoryId)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book ( isbn,title, type_id,category_id) VALUES (?,?,?,?)');
        $this->PDOLink->setValues([$isbn,$title, $typeId,$categoryId]);
        return $this->PDOLink->execute();
    }

}
