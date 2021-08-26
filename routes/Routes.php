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
     * | authentication level (A: all, L: logged user, NL: not logged user, M: book data maintenance (upload/edit), AD: admin)
     * @var array|string[][]
     * TODO hozzáadni paramétert ami jelöli melyik adatok kellenek a requestből -> url, phpinput, both
     */

    private array $routes = [

        ['POST', 'addquickdata', 'DataUploader', 'addQuickData', 'M'],
        ['POST', 'uploadbook', 'DataUploader', 'uploadFullBook', 'M'],
        ['GET', 'secondarydata\$1', 'BookDataGetter', 'getBookSecondaryData', 'A'],
        ['GET', 'logout', 'UserHandler', 'logOutUser', 'L'],
        ['GET', 'tokentouser', 'UserHandler', 'getUserByToken', 'A'],
        ['POST', 'login', 'UserHandler', 'loginUser', 'A'],
        ['POST', 'register', 'UserHandler', 'registerUser', 'NL'],
        ['GET', 'metadata', 'BookMetaData', 'getBookMetaData', 'A'],
        ['POST', 'booklist', 'BookListGetter', 'getBookList', 'A'],
        ['GET', 'primarydata\$1', 'BookDataGetter', 'getBookPrimaryData', 'A'],
        ['GET', 'datalist\$1\$2', 'DataListGetter', 'getDataList', 'A']
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
