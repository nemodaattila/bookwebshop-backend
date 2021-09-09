<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use database\PDOProcessorBuilder;
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
            page_number,format_id, weight, physical_size, short_description FROM book_description WHERE isbn=?');
        $this->PDOLink->setValues($isbn);
        $this->PDOLink->setFetchType('fetch');
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === null) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'GBDDISBNNE'], 500);
        }
        return $tempResult;
    }

    public function insert(string $isbn, int $targetAudience, int $publisher, int $language, int $year, int $page,
                           int    $format, ?int $weight, ?string $size, ?string $description)
    {

        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_description ( isbn,target_audience_id, publisher_id,
                              language_id, year, page_number, format_id,weight,physical_size, short_description) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $this->PDOLink->setValues(func_get_args());
        return $this->PDOLink->execute();
    }

    public function update(array $data, string $isbn)
    {
        if (empty($data)) return;
//        print_r($data);
        [$pdo, $dataSource] = PDOProcessorBuilder::getProcessorAndDataSource('update');
        $dataSource->addTable('book_description');
        $dataSource->addAttributes('book_description', array_keys($data));
        $dataSource->addWhereCondition('=', ['book_description.isbn', '?']);
        foreach (array_values($data) as $value)
            $dataSource->bindValue($value);
        $dataSource->bindValue($isbn);
        return $pdo->query($dataSource);
    }
}
