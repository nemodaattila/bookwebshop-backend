<?php

namespace rest;

use bookDataManipulator\BookUploader;
use classDbHandler\AuthorDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;

class DataUploader
{
    public function addQuickData(RequestParameters $parameters)
    {
        ["type" => $type, "data" => $value] = $parameters->getRequestData();

        switch ($type) {
            case 0:
            {
                $result = (new AuthorDBHandler())->insert($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
            case 1:
            {
                $result = (new PublisherDBHandler())->insert($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
            case 2:
            {
                $result = (new SeriesDBHandler())->insert($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
        }

    }

    public function uploadFullBook(RequestParameters $requestParameters)
    {
        $result = (new BookUploader())->addNewBook($requestParameters->getRequestData());
        throw new HttpResponseTriggerException(true, $result);

    }
}
