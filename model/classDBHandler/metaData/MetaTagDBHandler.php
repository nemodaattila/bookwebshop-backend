<?php

namespace classDbHandler\metaData;

use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;

/**
 * Class MetaTagDBHandler database connection / functions for table meta_tag
 * @package classDbHandler\metaData
 */
class MetaTagDBHandler
{
    /**
     * return all tags
     * @return array tags
     * @throws HttpResponseTriggerException bad processor type
     */
    public function get(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_tag");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
