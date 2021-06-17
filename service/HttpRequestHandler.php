<?php

namespace service;

use classModel\RequestParameters;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;

class HttpRequestHandler
{
    private string $routeBase = "";
    private RouteAnalyser $routeAnalyser;
    private RequestParameters $parameters;

    //DO authentication
    public function __construct()
    {
        try {
            $this->setRootConstant();
            $this->getRouteBaseFromRequest();
            $this->searchForExistingRoute();
            $this->getHttpRequestData();
            $this->loadRestClass();
        } catch (HttpResponseTriggerException $e) {
            $this->sendResponseBasedOnTriggerException($e);
        } catch (\Exception $e) {
            $this->sendResponseBasedOnError($e->getMessage());
        } catch (\Error $e) {
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
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $e->getHttpCode());
        $data = ['success' => $e->isSuccess(), "data" => $e->getData()];
        echo json_encode($data);
        die();
    }

    private function sendResponseBasedOnError(string $message)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . 500);
        echo $message;
    }

    private function getHttpRequestData()
    {
        $this->parameters = $this->routeAnalyser->getParameters();
        if (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) {
            if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                $putvars = [];
                parse_str(file_get_contents("php://input"), $putvars);
                $this->parameters->setRequestData($putvars);
            } else
                $this->parameters->setRequestData(VariableHelper::convertStdClassToArray(json_decode(file_get_contents('php://input'))));
        }
    }

    private function loadRestClass()
    {
        ['className' => $restClass, 'functionName' => $functionName] = $this->routeAnalyser->getRestData();
        $restClass = "\\rest\\" . $restClass;
        $class = new $restClass();
        $class->$functionName($this->parameters);
    }

}
