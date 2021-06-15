<?php

namespace service;

use routes\Routes;

class HttpRequestHandler
{
    private Routes $routes;
    private array $routeBase=[];

    public function __construct()
    {
        $this-> routes = new Routes();
        $this->analyzeRequest();
    }

    private function analyzeRequest()
    {
        var_dump($_SERVER['REQUEST_URI']);
        $separator = DIRECTORY_SEPARATOR;
        $urlStripper = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], "", ROOT);
        $request = explode($separator, (str_replace($urlStripper, "", $_SERVER['REQUEST_URI'])));
        var_dump($request);
        foreach ($request as $value) {
            if ($value !== '') {
                $this->routeBase[] = $value;
            }
        }
        var_dump($this->routeBase);

//        if (empty($this->target)) $this->target = $this->defaultTarget;
    }
}
