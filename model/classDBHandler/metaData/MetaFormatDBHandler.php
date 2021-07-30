<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class MetaFormatDBHandler database connection / functions to table meta_format
 * @package classDbHandler\metaData
 */
class MetaFormatDBHandler extends DBHandlerParent
{
    /**
     * returns formats grouped by book type
     * @return array formats in multidimensional associative array
     * @throws HttpResponseTriggerException bad BDO processor type
     */
    public function getGroupedByType(): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT id, type_id, name FROM meta_format');
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            if (!isset($result[$value['type_id']])) {
                $result[$value['type_id']] = [];
            }
            $result[$value['type_id']][$value['id']] = $value['name'];
        }
        return $result;
    }

}
