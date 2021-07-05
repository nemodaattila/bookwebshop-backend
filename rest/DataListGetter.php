<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;

class DataListGetter
{
    public function getDataList(RequestParameters $parameters)
    {
        [$type, $value] = $parameters->getUrlParameters();
        if (ucfirst($type)==="Author")
        {
            $this->getAuthorDataList($value);
        }
    }

    private function getAuthorDataList(string $value)
    {
        $ah = new AuthorDBHandler();
        $dl = $ah->getSpecificAuthorsWithLike($value);
        foreach ($dl as $key=>$value)
        {
            $dl[$key]=$value['Name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }
}
