<?php

namespace routes;

/**
 * Class Routes possible routes for Http request Router
 * @package routes
 */
class Routes
{

    /**
     * possible routes, parameters: type [GET, POST, PUT, DELETE] | url path | called class | called function
     * | authenticated users
     * @var array|string[][]
     * TODO hozzáadni paramétert ami jelöli melyik adatok kellenek a requestből -> url, phpinput, both
     */

    private array $routes = [
        ['GET', "logout", "UserHandler", "logOutUser", 2],
        ['GET', 'tokentouser', 'UserHandler', 'getUserByToken', 1],
        ['POST', "login", "UserHandler", "loginUser", 1],
        ['POST', "register", "UserHandler", "registerUser", 1],
        ['GET', 'metadata', 'BookMetaData', 'getBookMetaData', 1],
        ['POST', 'booklist', 'BookListGetter', 'getBookList', 1],
        ['GET', 'primarydata\$1', 'BookDataGetter', 'getBookPrimaryData', 1],
        ['GET', 'datalist\$1\$2', 'DataListGetter', 'getDataList', 1]
    ];

    /**
     * returns all routes
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
