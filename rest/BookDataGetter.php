<?php

namespace rest;

use classDbHandler\BookDataDBHAndler;
use classDbHandler\BookMetaDataDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;

class BookDataGetter
{
    public function getBookPrimaryData(RequestParameters $parameters )
    {
        $bookDataGetter = new BookDataDBHAndler();
        $result = $bookDataGetter->getPrimaryData($parameters->getUrlParameters()[0]);
        throw new HttpResponseTriggerException(true, $result);
    }

}
