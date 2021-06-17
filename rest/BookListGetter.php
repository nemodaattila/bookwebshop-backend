<?php

namespace rest;

use classModel\RequestParameters;

class BookListGetter
{
    public function getBookList(RequestParameters $parameters)
    {
        var_dump($parameters);
    }

}
