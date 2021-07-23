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
        $this->PDOLink->setCommand("SELECT name FROM author where id = ?");
        $this->PDOLink->setValues($id);
        $this->PDOLink->setFetchType('fetch');
        $result = $this->PDOLink->execute();
        return $result['name'];
    }

    function getSpecificAuthorsWithLike(string $pattern)
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("Select a.Name from Author as a where a.Name LIKE ?");
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }
}
