<?php

namespace rest;

use classDbHandler\BookMetaDataDBHandler;
use classDbHandler\metaData\MetaFormatDBHandler;
use classDbHandler\metaData\MetaLanguageDBHandler;
use classDbHandler\metaData\MetaMainCategoryDBHandler;
use classDbHandler\metaData\MetaSubCategoryDBHandler;
use classDbHandler\metaData\MetaTagDBHandler;
use classDbHandler\metaData\MetaTargetAudienceDBHandler;
use classDbHandler\metaData\MetaTypeDBHandler;
use exception\HttpResponseTriggerException;

class BookMetaData
{

    public function getBookMetaData()
    {
//        $metaDataGetter = new BookMetaDataDBHandler();
//        $result = $metaDataGetter->getAllMetaData();
        $result =  [
            'format' => (new MetaFormatDBHandler())->getGroupedByType(),
            'language' => (new MetaLanguageDBHandler())->get(),
            'mainCategory' => (new MetaMainCategoryDBHandler())->get(),
            'subCategory' => (new MetaSubCategoryDBHandler())->getGroupedByMainCategory(),
            'tag' => (new MetaTagDBHandler())->get(),
            'targetAudience' => (new MetaTargetAudienceDBHandler())->get(),
            'type' => (new MetaTypeDBHandler())->get(),
        ];
        throw new HttpResponseTriggerException(true, $result);
    }



}
