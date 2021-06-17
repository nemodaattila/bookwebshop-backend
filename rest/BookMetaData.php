<?php

namespace rest;

use classDbHandler\BookMetaDataDBHandler;
use exception\HttpResponseTriggerException;

class BookMetaData
{

    public function getBookMetaData()
    {
        $metaDataGetter = new BookMetaDataDBHandler();
        $result = $metaDataGetter->getAllMetaData();
        throw new HttpResponseTriggerException(true, $result);
    }

}
