<?php

namespace classDbHandler;

use exception\HttpResponseTriggerException;

/**
 * Class PublisherDBHandler database connector class for series (series table)
 * @package classDbHandler
 */
class SeriesDBHandler extends DBHandlerParent
{
    /**
     * returns series names which matches the gi
     * @param string $pattern pattern
     * @return array result names
     * @throws HttpResponseTriggerException wrong processor type, query error
     */
    function getSpecificSeriesWithLike(string $pattern): array
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select s.name from Series as s where s.name LIKE ?');
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }
}
