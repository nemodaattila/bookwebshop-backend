<?php

namespace classDbHandler;

use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;
use simpleDatabaseProcessor\SimplePDOProcessorParent;

class DBHandlerParent
{
    protected ?SimplePDOProcessorParent $PDOLink;
    private string $actualType;
    private bool $actualSimple = true;

    /**
     * @param string $type creates a PDO helper class, with live PDO connection
     * @throws HttpResponseTriggerException inappropriate processor type
     */
    protected function createPDO(string $type, bool $isSimple = true)
    {
        if (!isset($this->PDOLink)) {
            $this->PDOLink = PDOProcessorBuilder::getProcessor($type, $isSimple);
            $this->actualType = $type;
            $this->actualSimple = $isSimple;
        } else if (isset($this->PDOLink) && ($type !== $this->actualType || $isSimple !== $this->actualSimple)) {
            $this->PDOLink->nullPDO();
            $this->PDOLink = PDOProcessorBuilder::getProcessor($type, $isSimple);
            $this->actualSimple = $isSimple;
            $this->actualType = $type;
            $this->actualSimple = $isSimple;
        }

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
