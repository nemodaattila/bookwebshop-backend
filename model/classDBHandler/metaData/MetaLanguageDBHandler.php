<?php

namespace classDbHandler\metaData;

use classDbHandler\DBHandlerParent;
use exception\HttpResponseTriggerException;

/**
 * Class MetaLanguageDBHandler database connection / functions to table meta_language
 * @package classDbHandler\metaData bad BDO processor type
 */
class MetaLanguageDBHandler extends DBHandlerParent
{
    /**
     * returns all book language
     * @return array languages
     * @throws HttpResponseTriggerException
     */
    public function get(): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("SELECT id,  name FROM meta_language");
        $tempResult = $this->PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
