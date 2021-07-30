<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;

/**
 * Class MetaSubCategoryDBHandler database connection / functions to table meta_subcategory
 * @package classDbHandler\metaData
 */
class MetaSubCategoryDBHandler extends DBHandlerParent
{
    /**
     * return all theme subcategories of books grouped by category
     * @return array subcategories
     * @throws HttpResponseTriggerException bad BDO processor type
     */
    public function getGroupedByMainCategory(): array
    {
        $this->createPDO('select');
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand('SELECT id, main_category_id, name FROM meta_subcategory');
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
