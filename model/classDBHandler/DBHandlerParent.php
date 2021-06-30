<?php

namespace classDbHandler;

use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;
use simpleDatabaseProcessor\SimpleSelectPDOProcessor;

class DBHandlerParent
{
    protected ?SimpleSelectPDOProcessor $PDOLink;

    /**
     * @param string $type creates a PDO helper class, with live PDO connection
     * @throws HttpResponseTriggerException inappropriate processor type
     */
    protected function createPDO(string $type, bool $isSimple = true)
    {
        if (isset($this->PDOLink))
            $this->PDOLink->nullPDO();
        $this->PDOLink = PDOProcessorBuilder::getProcessor($type, $isSimple);
    }

    /**
     *  destroying connection at script end
     */
    public function __destruct()
    {
        if (isset($this->PDOLink))
            $this->PDOLink->nullPDO();
    }
}
