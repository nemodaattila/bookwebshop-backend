<?php

namespace rest;

use classDbHandler\AuthorDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\RequestParameters;
use exception\HttpResponseTriggerException;

/**
 * Http request processor
 * class for collecting book data with LIKE patterns (for datalist on frontend)
 * Class DataListGetter
 * @package rest
 */
class DataListGetter
{
    /**
     * calls function based on request parameter (Author, Publisher or Series)
     * @param RequestParameters $parameters http request parameters
     * @throws HttpResponseTriggerException functions throw result in exception (it's ok not error)
     */
    public function getDataList(RequestParameters $parameters)
    {
        [$type, $value] = $parameters->getUrlParameters();
        if (ucfirst($type) === 'Author') {
            $this->getAuthorDataList($value);
        }
        if (ucfirst($type) === 'Publisher') {
            $this->getPublisherDataList($value);
        }
        if (ucfirst($type) === 'Series') {
            $this->getSeriesDataList($value);
        }
    }

    /**
     * gets authors that match the given pattern
     * @param string $value pattern
     * @throws HttpResponseTriggerException throws result in exception (it's ok not error), db error
     */
    private function getAuthorDataList(string $value)
    {
        $ah = new AuthorDBHandler();
        $dl = $ah->getSpecificAuthorsWithLike($value);
        foreach ($dl as $key => $value) {
            $dl[$key] = $value['Name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }

    /**
     * gets publishers that match the given pattern
     * @param string $value pattern
     * @throws HttpResponseTriggerException throws result in exception (it's ok not error), db error
     */
    private function getPublisherDataList(string $value)
    {
        $ph = new PublisherDBHandler();
        $dl = $ph->getSpecificPublisherWithLike($value);
        foreach ($dl as $key => $value) {
            $dl[$key] = $value['name'];
        }
        throw new HttpResponseTriggerException(true, $dl);
    }

    /**
     * gets series that match the given pattern
     * @param string $value pattern
     * @throws HttpResponseTriggerException throws result in exception (it's ok not error), db error
     */
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
