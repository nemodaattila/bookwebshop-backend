<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;

/**
 * Class MetaMainCategoryDBHandler database connection/ functions to table meta_main_category
 * @package classDbHandler\metaData
 */
class MetaMainCategoryDBHandler extends DBHandlerParent
{
    /**
     * returns all main theme categories of books
     * @return array categories
     * @throws HttpResponseTriggerException bad BDO processor type
     */
    public function get(): array
    {
        $this->createPDO('select');
        $this->PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $this->PDOLink->setCommand("SELECT id,  name FROM meta_main_category");
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

}
