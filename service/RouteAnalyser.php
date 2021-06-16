<?php

namespace service;

use routes\Routes;

class RouteAnalyser
{
    private Routes $routes;
    private string $routeBase;

    public function __construct($routeBase)
    {
        $this->routeBase = $routeBase;
        $this->routes = new Routes();

    }
}
