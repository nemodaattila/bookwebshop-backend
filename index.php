<?php

use service\HttpRequestHandler;

require_once "./vendor/autoload.php";
DEFINE("ROOT", $_SERVER['DOCUMENT_ROOT'] . '/BookWebShopBackend/');
new HttpRequestHandler();


