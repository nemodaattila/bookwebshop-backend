<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class MetaTargetAudienceDBHandler database connection/ functions for table meta_target_audience
 * @package classDbHandler\metaData
 */
class MetaTargetAudienceDBHandler extends DBHandlerParent
{
    /**
     * return all target audience categories
     * @return array target audiences
     * @throws HttpResponseTriggerException bad processor type
     */
    public function get(): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("SELECT id,  name FROM meta_target_audience");
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
