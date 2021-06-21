<?php

namespace rest;

use classDbHandler\BookMetaDataDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;

class BookDataGetter
{
    public function getBookPrimaryData(RequestParameters $parameters )
    {
        var_dump($parameters);
//        $metaDataGetter = new BookMetaDataDBHandler();
//        $result = $metaDataGetter->getAllMetaData();
//        throw new HttpResponseTriggerException(true, $result);
    }
}
