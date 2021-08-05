<?php

namespace classDbHandler;

use exception\HttpResponseTriggerException;

class DiscountDBHandler extends DBHandlerParent
{

    public function getNameById(int $id): string
    {
        $this->createPDO('select');
        $this->PDOLink->setCommand('SELECT d.name FROM discount as d WHERE id=?');
        $this->PDOLink->setFetchType('fetch');
        $this->PDOLink->setValues($id);
        $tempResult = $this->PDOLink->execute();
        if ($tempResult === false) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'DDBHIDNE']);
        }
        return $tempResult['name'];
    }
}
