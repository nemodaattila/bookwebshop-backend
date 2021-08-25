<?php

namespace classDbHandler;

use exception\HttpResponseTriggerException;

/**
 * Class AuthorDBHandler database connector / functions to table author
 * @package classDbHandler
 */
class AuthorDBHandler extends DBHandlerParent
{
    /**
     * returns an author's name by author's id
     * @param int $id id of the author
     * @return string name of the author
     * @throws HttpResponseTriggerException bad processor type
     */
    public function getNameByID(int $id): string
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT name FROM author where id = ?');
        $this->PDOLink->setValues($id);
        $this->PDOLink->setFetchType('fetch');
        $result = $this->PDOLink->execute();
        return $result['name'];
    }

    function getIdByName(string $name): int
    {

        $this->createPDO('select');
        $this->PDOLink->setCommand('Select p.id from author as p where p.name = ?');
        $this->PDOLink->setValues($name);
        $this->PDOLink->setFetchType('fetch');
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'ADBHNDNE']);
        }
        return $tempResult['id'];
    }

    /**
     * returns all author's names which matches LIKE pattern
     * @param string $pattern pattern to match authors
     * @return array name of authors
     * @throws HttpResponseTriggerException bad processor type, query error
     */
    function getWithLike(string $pattern): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select a.Name from Author as a where a.Name LIKE ?');
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }

    function insert(string $name): bool
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO author (name) VALUES (?)');
        $this->PDOLink->setValues($name);
        return $this->PDOLink->execute();

    }
}
