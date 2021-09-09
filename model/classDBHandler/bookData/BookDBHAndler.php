<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use database\PDOProcessorBuilder;
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
     * @return array|bool data in array
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
        $this->PDOLink->setValues([$isbn, $title, $typeId, $categoryId]);
        return $this->PDOLink->execute();
    }

    public function update(array $data, string $isbn)
    {
        if (empty($data)) return;
//        print_r($data);
        [$pdo, $dataSource] = PDOProcessorBuilder::getProcessorAndDataSource('update');
        $dataSource->addTable('book');
        $dataSource->addAttributes('book', array_keys($data));
        $dataSource->addWhereCondition('=', ['book.isbn', '?']);
        foreach (array_values($data) as $value)
            $dataSource->bindValue($value);
        $dataSource->bindValue($isbn);
        return $pdo->query($dataSource);
    }

    public function updateIsbn(string $originalIsbn, string $newIsbn)
    {
        $this->createPDO('update');
        $this->PDOLink->setCommand('UPDATE book SET isbn = ? WHERE isbn=?');
        $this->PDOLink->setValues([$newIsbn, $originalIsbn]);
        $this->PDOLink->execute();
    }

    public function delete(string $isbn)
    {
        $this->createPDO('delete');
        $this->PDOLink->setCommand('DELETE FROM book WHERE isbn = ? ');
        $this->PDOLink->setValues([$isbn]);
        return $this->PDOLink->execute();

    }

}
