<?php

namespace classDbHandler;

class PublisherDBHandler extends DBHandlerParent
{

    function getSpecificPublisherWithLike(string $pattern)
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand("Select p.name from Publisher as p where p.name LIKE ?");
        $this->PDOLink->setValues('%'.$pattern.'%');
        return $this->PDOLink->execute();
    }
}
