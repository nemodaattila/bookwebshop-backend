<?php

namespace routes;

class Routes
{

    /**
     * adds a route to the router
     * @param string $httpMethod request type - get/post/put/delete
     * @param string $url - request url: user/$1
     * @param string $classname called resthandler class
     * @param string $task - function to be called
     * @param bool $authRequired - authentication level all/user/admin / ?? check PHP JWT
     * @param bool $response is encoded
     */

    private array $routes =[
        ['GET', 'metadata', 'BookMetaData', 'getBookMetaData', 'all', true],
    ];

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }


}
