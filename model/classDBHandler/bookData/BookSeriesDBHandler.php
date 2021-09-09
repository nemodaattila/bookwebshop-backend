<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * db connector class to database book_series
 */
class BookSeriesDBHandler extends DBHandlerParent
{
    /**
     * returns series id of a book based on isbn
     * @param string $isbn
     * @return int|null
     * @throws HttpResponseTriggerException
     */
    function getSeriesIdByIsbn(string $isbn): ?int
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select s.series_id from book_series as s where s.isbn = ?');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'BSDBNE']);
        }
        if (empty($tempResult)) {
            return null;
        }
        return $tempResult[0]['series_id'];
    }

    public function insert(string $isbn, int $seriesId)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_series (isbn,series_id) VALUES (?,?)');
        $this->PDOLink->setValues([$isbn, $seriesId]);
        return $this->PDOLink->execute();
    }

    public function update(int $series, string $isbn)
    {
//        if (empty($price)) return;
        $this->createPDO('update');
        $this->PDOLink->setCommand('UPDATE book_series SET series_id = ? WHERE isbn = ?');
        $this->PDOLink->setValues([$series, $isbn]);
        return $this->PDOLink->execute();
    }

    public function delete(string $isbn)
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand('DELETE FROM book_series WHERE isbn = ? ');
        $this->PDOLink->setValues([$isbn]);
        return $this->PDOLink->execute();

    }
}
