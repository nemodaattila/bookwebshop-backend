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
    function getWithLike(string $pattern): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select p.name from publisher as p where p.name LIKE ?');
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }

    function getIdByName(string $name): int
    {

        $this->createPDO('select');
        $this->PDOLink->setCommand('Select p.id from publisher as p where p.name = ?');
        $this->PDOLink->setValues($name);
        $this->PDOLink->setFetchType('fetch');
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'PDBHNDNE']);
        }
        return $tempResult['id'];
    }

    /**
     * returns a publishers name based on id
     * @param int $id
     * @return string
     * @throws HttpResponseTriggerException
     */
    function getNameById(int $id): string
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

    function insert(string $name): bool
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO publisher (name) VALUES (?)');
        $this->PDOLink->setValues($name);
        return $this->PDOLink->execute();

    }
}
