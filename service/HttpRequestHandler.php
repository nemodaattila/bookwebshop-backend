<?php

namespace service;

use exception\HttpResponseTriggerException;

class HttpRequestHandler
{
    private string $routeBase="";

    public function __construct()
    {
        try {

            $this->setRootConstant();
            $this->getRouteBaseFromRequest();
            $this->searchForExistingRoute();
        }
        catch(HttpResponseTriggerException $e)
        {
            $this->sendResponseBasedOnTriggerException($e);
        }
    }

    private function setRootConstant()
    {
        $filename = str_replace('/', '\\', $_SERVER['SCRIPT_NAME']);
        $root = str_replace('index.php',"", strtolower($filename));
        DEFINE("ROOT", $root);
    }

    private function getRouteBaseFromRequest()
    {
        $request = strtolower($_SERVER['REQUEST_URI']);
        $urlStripper = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], "", ROOT);
        $request = str_replace(['//','/'], "\\", $request);
        $request = str_replace($urlStripper, "", $request);
        $this->routeBase = $request;
    }

    private function searchForExistingRoute()
    {
        $ra = new RouteAnalyser($this->routeBase);
    }

    private function sendResponseBasedOnTriggerException(HttpResponseTriggerException $e)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $e->getCode());
        $data = ['success'=>$e->isSuccess(), "data"=>$e->getData()];
        echo json_encode($data);
        die();
    }
}
