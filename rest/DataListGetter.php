<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;

class DataListGetter
{
    public function getDataList(RequestParameters $parameters)
    {
        [$type, $value] = $parameters->getUrlParameters();
        if (ucfirst($type) === "Author") {
            $this->getAuthorDataList($value);
        }
        if (ucfirst($type) === "Publisher") {
            $this->getPublisherDataList($value);
        }
        if (ucfirst($type) === "Series") {
            $this->getSeriesDataList($value);
        }
    }

    private function getAuthorDataList(string $value)
    {
        $ah = new AuthorDBHandler();
        $dl = $ah->getSpecificAuthorsWithLike($value);
        foreach ($dl as $key => $value) {
            $dl[$key] = $value['Name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }

    private function getPublisherDataList(string $value)
    {
        $ph = new PublisherDBHandler();
        $dl = $ph->getSpecificPublisherWithLike($value);
        foreach ($dl as $key => $value) {
            $dl[$key] = $value['name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }

    private function getSeriesDataList(string $value)
    {
        $ph = new SeriesDBHandler();
        $dl = $ph->getSpecificSeriesWithLike($value);
        foreach ($dl as $key => $value) {
            $dl[$key] = $value['name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }
}
