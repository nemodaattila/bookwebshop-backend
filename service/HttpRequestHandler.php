<?php

namespace service;

use classModel\RequestParameters;
use Error;
use Exception;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;

/**
 * loads the corresponding HTTP request processor class
 * based on http request
 * Class HttpRequestHandler
 * @package service
 */
class HttpRequestHandler
{
    /**
     * @var string route to be processed, from http request
     * e.g. www.example.com/user/123 -> user/123
     */
    private string $routeBase = '';

    /**
     * @var RouteAnalyser instance of the RouteAnalyser
     */
    private RouteAnalyser $routeAnalyser;

    /**
     * @var RequestParameters parameters from http request
     */
    private RequestParameters $parameters;

    //DO authentication
    public function __construct()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                $this->addCorsHeaders();
            } else {
                $this->addCorsOriginHeader();
                $this->setRootConstant();
                $this->getRouteBaseFromRequest();
                $this->searchForExistingRoute();
                $this->getHttpRequestData();
                $this->authenticateUser();
                $this->authenticationTaskGuard();
                $this->loadRestClass();
            }

        } catch (HttpResponseTriggerException $e) {
            $this->sendResponseBasedOnTriggerException($e);
        } catch (Exception $e) {
            $this->sendResponseBasedOnError($e->getMessage(), $e->getFile(), $e->getLine());
        } catch (Error $e) {
            $this->sendResponseBasedOnError($e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    /**
     * sends cors headers
     */
    private function addCorsHeaders()
    {

        //DO expand cors handling
//        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Origin: http://localhost:4200');
        header('Access-Control-Allow-Headers: X-Requested-With, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding,Content-Type');
        header('Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS, DELETE');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Expose-Headers: TokenExpirationTime');

    }

    private function addCorsOriginHeader()
    {
        header('Access-Control-Allow-Origin: http://localhost:4200');
//        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Expose-Headers: TokenExpirationTime');
    }

    /**
     * determines the root of project from url
     * @example www.example.com/user/123 -> www.example.com
     */
    private function setRootConstant()
    {
        $filename = str_replace('/', '\\', $_SERVER['SCRIPT_NAME']);
        $root = str_replace('index.php', '', strtolower($filename));
        DEFINE('ROOT', $root);
    }

    /**
     * determines the route path form the request url
     * @example www.example.com/user/123 -> user/123
     */
    private function getRouteBaseFromRequest()
    {
        $request = strtolower($_SERVER['REQUEST_URI']);
        $urlStripper = str_replace($_SERVER['CONTEXT_DOCUMENT_ROOT'], '', ROOT);
        $request = str_replace(['//', '/'], '\\', $request);
        $request = str_replace($urlStripper, '', $request);
        $this->routeBase = $request;
    }

    /**
     * determines the appropriate route for the url
     * @throws Exception if there is no url connected to the url
     */
    private function searchForExistingRoute()
    {
        $this->routeAnalyser = new RouteAnalyser($this->routeBase);
        $routeExists = $this->routeAnalyser->processGivenRoute();
        if (!$routeExists) {
            throw new Exception('Route not exists: ' . $this->routeBase);
        }
    }

    /**
     * collects data from http request body, converts it if necessary
     * @throws Exception if the data is in an inappropriate format
     */
    private function getHttpRequestData()
    {
        $this->parameters = $this->routeAnalyser->getParameters();

        if (isset($_SERVER['CONTENT_TYPE'])) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'PUT':
//                    $putVars = file_get_contents('php://input');
//                    print_r($putVars);
//                    foreach ($putVars as $key=>$value)
//                    {
//                        $putVars[$key] = json_decode($value);
//                    }
//                    var_dump($putVars);
//                    if (count($putVars) === 1) $putVars = $putVars[0];
//                    $this->parameters->setRequestData($putVars);
//                    break;
                case 'POST':
                    $requestData = file_get_contents('php://input');
                    $decodedData = json_decode($requestData);
//                    print_r($decodedData);

                    if ($decodedData === null) {
                        $this->parameters->setRequestData([$requestData]);
                    } else {
                        if (gettype($decodedData) === 'array') {
                            $this->parameters->setRequestData($decodedData);
                        } elseif (gettype($decodedData) === 'object') {
                            $this->parameters->setRequestData(VariableHelper::convertStdClassToArray($decodedData));
                        } else throw new Exception('POST REQUEST DATA INCORRECT FORMAT');
                    }
//                    print_r($_FILES);
//                    var_dump(get_object_vars($this->parameters->getRequestData()[0]));
                    break;
            }
        }
    }

    /**
     * checks user authentication
     */
    private function authenticateUser()
    {
        if (isset(getallheaders()['Authorization'])) {
            $token = filter_var(getallheaders()['Authorization'], FILTER_SANITIZE_STRING);
            if ($token !== null) {
                $as = Authentication::getInstance();
                $as->authenticateUserByToken($token);
            }
        }
    }

    /**
     * checks if user is entitled to use the given function
     * @throws HttpResponseTriggerException
     */
    private function authenticationTaskGuard()
    {
        //TODO
        $as = Authentication::getInstance();
        $token = $as->getTokenObj();

        ['authentication' => $authenticationLevel] = $this->routeAnalyser->getRestData();
        if (($authenticationLevel !== 'A' && $authenticationLevel !== 'NL') && $token === null) {
            throw new HttpResponseTriggerException(false, ['errorCode' => 'TAF', 'type' => 1]);
        }
        switch ($authenticationLevel) {
            case 'NL':
                if ($token !== null)
                    throw new HttpResponseTriggerException(false, ['errorCode' => 'TAF', 'type' => 2]);
                break;
            case 'M':
                if ($token->getAuthorizationLevel() < 2)
                    throw new HttpResponseTriggerException(false, ['errorCode' => 'TAF', 'type' => 3]);
                break;
            case 'AD':
                if ($token->getAuthorizationLevel() < 3)
                    throw new HttpResponseTriggerException(false, ['errorCode' => 'TAF', 'type' => 4]);
                break;
        }
    }

    /**
     * loads a http request processor class based on url
     */
    private function loadRestClass()
    {
        ['className' => $restClass, 'functionName' => $functionName] = $this->routeAnalyser->getRestData();
        $restClass = '\\rest\\' . $restClass;
        $class = new $restClass();
        $class->$functionName($this->parameters);
    }

    /**
     * send a http response based on a HttpResponseTriggerException
     * @param HttpResponseTriggerException $e specific exception for responses
     */
    private function sendResponseBasedOnTriggerException(HttpResponseTriggerException $e)
    {
        $this->addTokenExpirationTimeToHeader();
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . $e->getHttpCode());
        $data = ['success' => $e->isSuccess(), 'data' => $e->getData()];
        echo json_encode($data);
        die();
    }

    private function addTokenExpirationTimeToHeader()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
            $as = Authentication::getInstance();
            $state = $as->getTokenState();
            if ($state[0] === true) {
                header('TokenExpirationTime: ' . $as->getTokenObj()->getExpirationTime());
            }
        }
    }

    /**
     * sends a http response based on error parameters
     * @param string $message error message
     * @param string $file filename from which the error was thrown
     * @param int $line line from which the error was thrown
     */
    private function sendResponseBasedOnError(string $message, string $file, int $line)
    {
        //DO save message to log instead of echo
        $this->addTokenExpirationTimeToHeader();
        header($_SERVER['SERVER_PROTOCOL'] . ' ' . 500);
        echo $message . ' - ' . $file . ':' . $line;
    }

}
