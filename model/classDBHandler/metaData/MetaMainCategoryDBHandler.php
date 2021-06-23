<?php

namespace classDbHandler\metaData;

use database\PDOProcessorBuilder;

class MetaMainCategoryDBHandler
{
    public function get(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_main_category");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

}
