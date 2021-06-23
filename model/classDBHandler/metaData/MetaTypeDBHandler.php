<?php

namespace classDbHandler\metaData;

use database\PDOProcessorBuilder;

class MetaTypeDBHandler
{
    public function get(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_type");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
