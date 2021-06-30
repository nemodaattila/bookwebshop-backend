<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class MetaTypeDBHandler database connection / function to table meta_type
 * @package classDbHandler\metaData
 */
class MetaTypeDBHandler extends DBHandlerParent
{
    /**
     * returns all book types
     * @return array book types
     * @throws HttpResponseTriggerException bad processor type
     */
    public function get(): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("SELECT id,  name FROM meta_type");
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
