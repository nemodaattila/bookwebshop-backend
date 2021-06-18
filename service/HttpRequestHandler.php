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
            $this->addCorsHeaders();
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

    private function addCorsHeaders()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding");
        header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE");
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
        if (isset($_SERVER['CONTENT_TYPE'])) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case "PUT":
                    $putvars = [];
                    parse_str(file_get_contents("php://input"), $putvars);
                    $this->parameters->setRequestData($putvars);
                    break;
                case "POST":
                    $requestData = file_get_contents('php://input');
                    $decodedData = json_decode($requestData);
                    if ($decodedData === null) {
                        $this->parameters->setRequestData([$requestData]);
                    } else {
                        if (gettype($decodedData) === 'array') {
                            $this->parameters->setRequestData($decodedData);
                        }
                        elseif (gettype($decodedData) === 'object') {
                            $this->parameters->setRequestData(VariableHelper::convertStdClassToArray($decodedData));
                        }
                        else throw new \Exception('POST REQUEST DATA INCORRECT FORMAT');

                    }
                    break;
            }
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
