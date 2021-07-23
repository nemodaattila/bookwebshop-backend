<?php

namespace classDbHandler;

class SeriesDBHandler extends DBHandlerParent
{
    function getSpecificSeriesWithLike(string $pattern)
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("Select s.name from Series as s where s.name LIKE ?");
        $this->PDOLink->setValues('%' . $pattern . '%');
        return $this->PDOLink->execute();
    }
}
