<?php

namespace rest;

use classDbHandler\DiscountDBHandler;
use classDbHandler\metaData\MetaFormatDBHandler;
use classDbHandler\metaData\MetaLanguageDBHandler;
use classDbHandler\metaData\MetaMainCategoryDBHandler;
use classDbHandler\metaData\MetaSubCategoryDBHandler;
use classDbHandler\metaData\MetaTagDBHandler;
use classDbHandler\metaData\MetaTargetAudienceDBHandler;
use classDbHandler\metaData\MetaTypeDBHandler;
use exception\HttpResponseTriggerException;

/**
 * collects all metadata connected to books :
 * format, type, language, main and subcategory, tag, target audience
 * Class BookMetaData
 * @package rest
 */
class BookMetaData
{
    /**
     * calls every corresponding Db handler for every metadata type ,
     * and throws an exception as result with the data in it in array form
     * @throws HttpResponseTriggerException result data
     */
    public function getBookMetaData()
    {
        $result = [
            'format' => (new MetaFormatDBHandler())->getGroupedByType(),
            'language' => (new MetaLanguageDBHandler())->get(),
            'mainCategory' => (new MetaMainCategoryDBHandler())->get(),
            'subCategory' => (new MetaSubCategoryDBHandler())->getGroupedByMainCategory(),
            'tag' => (new MetaTagDBHandler())->get(),
            'targetAudience' => (new MetaTargetAudienceDBHandler())->get(),
            'type' => (new MetaTypeDBHandler())->get(),
            "discountType" => (new DiscountDBHandler())->get()
        ];
        throw new HttpResponseTriggerException(true, $result);
    }
}
