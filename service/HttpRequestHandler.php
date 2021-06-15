<?php

namespace service;

use routes\Routes;

class HttpRequestHandler
{
    private Routes $routes;

    public function __construct()
    {
        $this-> routes = new Routes();
    }
}
