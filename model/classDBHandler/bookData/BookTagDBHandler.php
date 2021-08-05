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
}
