<?php

namespace classDbHandler;

use database\PDOProcessorBuilder;

class AuthorDBHandler
{
    public function getNameByID(int $id): string
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT name FROM author where id = ?");
        $PDOLink->setValues($id);
        $PDOLink->setFetchType('fetch');
        $result = $PDOLink->execute();
        return $result['name'];
    }
}
