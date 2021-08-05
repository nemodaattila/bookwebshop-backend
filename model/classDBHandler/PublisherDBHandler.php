<?php

namespace classDbHandler;

use exception\HttpResponseTriggerException;

/**
 * Class PublisherDBHandler database connector class for publishers (publisher table)
 * @package classDbHandler
 */
class PublisherDBHandler extends DBHandlerParent
{

    /**
     * returns publisher's names which matches the gi
     * @param string $pattern pattern
     * @return array result name
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    function getSpecificPublisherWithLike(string $pattern): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select p.name from publisher as p where p.name LIKE ?');
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }

    /**
     * returns a publishers name based on id
     * @param int $id
     * @return string
     * @throws HttpResponseTriggerException
     */
    function getPublisherNameById(int $id): string
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select p.name from publisher as p where p.id = ?');
        $this->PDOLink->setValues($id);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDBHIDNE']);
        }
        return $tempResult[0]['name'];
    }
}
