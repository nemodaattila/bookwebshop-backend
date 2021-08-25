<?php

namespace classDbHandler\bookData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * db connector class to table book_tag
 */
class BookTagDBHandler extends DBHandlerParent
{
    /**
     * returns all tags linked to a book based on isbn
     * @param string $isbn
     * @return array
     * @throws HttpResponseTriggerException
     */
    function getTagsByIsbn(string $isbn): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select bt.tag_id from book_tag as bt where bt.isbn = ?');
        $this->PDOLink->setValues($isbn);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'BSDBNE']);
        }
        if (empty($tempResult)) {
            return [];
        }

        $tags = [];
        foreach ($tempResult as $value) {
            $tags[] = $value['tag_id'];
        }
        return $tags;
    }

    public function insert(string $isbn, int $tagId)
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO book_tag (isbn,tag_id) VALUES (?,?)');
        $this->PDOLink->setValues([$isbn, $tagId]);
        return $this->PDOLink->execute();
    }
}
