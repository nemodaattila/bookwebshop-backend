<?php

namespace classDbHandler\metaData;

use database\PDOProcessorBuilder;

class MetaSubCategoryDBHandler
{
    public function getGroupedByMainCategory(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id, main_category_id, name FROM meta_subcategory");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            if (!isset($result[$value['main_category_id']])) {
                $result[$value['main_category_id']] = [];
            }
            $result[$value['main_category_id']][$value['id']] = $value['name'];
        }
        return $result;
    }
}
