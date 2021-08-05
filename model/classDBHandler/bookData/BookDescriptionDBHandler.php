<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * DB connector class to table: book_description
 */
class BookDescriptionDBHandler extends DBHandlerParent
{
    /**
     * returns record of a book from the table based on isbn
     * @param string $isbn
     * @return array
     * @throws HttpResponseTriggerException sql error, no book with isbn
     */
    public function getByIsbn(string $isbn): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT isbn, target_audience_id, publisher_id, language_id, year,
            page_number, weight, physical_size, short_description FROM book_description WHERE isbn=?');
        $this->PDOLink->setValues($isbn);
        $this->PDOLink->setFetchType('fetch');
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === null) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'GBDDISBNNE'], 500);
        }
        return $tempResult;
    }
}
