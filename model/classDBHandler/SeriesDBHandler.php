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

    /**
     * returns the name of a series based on id
     * @param int $id
     * @return string
     * @throws HttpResponseTriggerException
     */
    function getSeriesNameById(int $id): string
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('Select s.name from series as s where s.id = ?');
        $this->PDOLink->setValues($id);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'SDBHIDNE']);
        }
        return $tempResult[0]['name'];
    }

    function addNewSeries(string $name): bool
    {
        $this->createPDO('insert');
        $this->PDOLink->setCommand('INSERT INTO series (name) VALUES (?)');
        $this->PDOLink->setValues($name);
        return $this->PDOLink->execute();

    }
}
