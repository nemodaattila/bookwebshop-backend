<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;
use service\SessionHandler;

class DataUploader
{
    public function addQuickData(RequestParameters $parameters)
    {
        ["type" => $type, "data" => $value] = $parameters->getRequestData();

        switch ($type)
        {
            case 0:
            {
                $result= (new AuthorDBHandler())->addNewAuthor($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
            case 1:
            {
                $result= (new PublisherDBHandler())->addNewPublisher($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
            case 2:
            {
                $result= (new SeriesDBHandler())->addNewSeries($value);
                throw new HttpResponseTriggerException(true, $result);
                break;
            }
        }

    }
}
