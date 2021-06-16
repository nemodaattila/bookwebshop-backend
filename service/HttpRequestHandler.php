<?php

namespace service;

use exception\HttpResponseTriggerException;

class HttpRequestHandler
{
    private string $routeBase = "";
    private RouteAnalyser $routeAnalyser;

    public function __construct()
    {
        try {

            $this->setRootConstant();
            $this->getRouteBaseFromRequest();
            $this->searchForExistingRoute();
        } catch (HttpResponseTriggerException $e) {
            $this->sendResponseBasedOnTriggerException($e);
        }
        catch (\Exception $e)
        {
            $this->sendResponseBasedOnError($e->getMessage());
        }
        catch (\Error $e)
        {
            $this->sendResponseBasedOnError($e->getMessage());
        }
    }

    private function setRootConstant()
    {
        $filename = str_replace('/', '\\', $_SERVER['SCRIPT_NAME']);
        $root = str_replace('index.php', "", strtolower($filename));
        DEFINE("ROOT", $root);
    }

    private function getRouteBaseFromRequest()
    {
        $request = strtolower($_SERVER['REQUEST_URI']);
        $urlStripper = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], "", ROOT);
        $request = str_replace(['//', '/'], "\\", $request);
        $request = str_replace($urlStripper, "", $request);
        $this->routeBase = $request;
    }

    private function searchForExistingRoute()
    {
        $this->routeAnalyser = new RouteAnalyser($this->routeBase);
        $routeExists = $this->routeAnalyser->processGivenRoute();
        if (!$routeExists) {
            throw new \Exception('Route not exists: ' . $this->routeBase);
        }

    }

    private function sendResponseBasedOnTriggerException(HttpResponseTriggerException $e)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $e->getCode());
        $data = ['success' => $e->isSuccess(), "data" => $e->getData()];
        echo json_encode($data);
        die();
    }

    private function sendResponseBasedOnError(string $message)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . 500);
        echo $message;
    }
}
