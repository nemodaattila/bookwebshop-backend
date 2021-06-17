<?php

namespace service;

use classModel\RequestParameters;
use routes\Routes;

class RouteAnalyser
{
    private array $routes;
    private string $routeBase;
    private RequestParameters $parameters;
    private array $restData;

    /**
     * @return RequestParameters
     */
    public function getParameters(): RequestParameters
    {
        return $this->parameters;
    }

    /**
     * @return array
     */
    public function getRestData(): array
    {
        return $this->restData;
    }

    public function __construct($routeBase)
    {
        $this->routeBase = $routeBase;
        $this->routes = (new Routes())->getRoutes();
        $this->parameters = new RequestParameters();
    }

    public function processGivenRoute()
    {
        foreach ($this->routes as $route)
        {
            $real = $this->identifyHeader($route[0], $route[1]);
            if ($real)
            {
                $this->restData = ['className'=>$route[2], 'functionName'=>$route[3], 'authentication'=>$route[4]];
                return true;
            }
        }
        return false;
    }

    private function identifyHeader($httpMethod, $path): bool
    {
        $path = str_replace(['//','/'], "\\", $path);
        $url = explode('\\', $this->routeBase);
        $path = explode('\\', $path);
        if (count($path) !== count($url)) {
            return false;
        }
        $length = count($path);
        for ($i = 0; $i < $length; $i++) {

            if ($path[$i] !== $url[$i]) {

                if (preg_match('/\$([0-9]+?)/', $path[$i]) !== 1) {
                    $this->parameters->reset();
                    return false;
                }
                $this->parameters->addUrlParameter(filter_var($path[$i], FILTER_SANITIZE_STRING));
            }
        }
        return true;
    }


}
