<?php

namespace service;

use routes\Routes;

class HttpRequestHandler
{
    private Routes $routes;
    private string $routeBase="";

    public function __construct()
    {
        $this-> routes = new Routes();
        $this->setRootConstant();
        $this->analyzeRequest();
    }

    private function setRootConstant()
    {
        $filename = str_replace('/', '\\', $_SERVER['SCRIPT_NAME']);
        $root = str_replace('index.php',"", strtolower($filename));
        DEFINE("ROOT", $root);
    }

    private function analyzeRequest()
    {
        $request = strtolower($_SERVER['REQUEST_URI']);
        $urlStripper = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], "", ROOT);
        $request = str_replace('/', '\\', $request);
        $request = str_replace($urlStripper, "", $request);
        $this->routeBase = $request;
        echo json_encode($this->routeBase);
    }
}
