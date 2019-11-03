<?php

namespace App\Jobs;


use Core\AbstractInterface\ARouter;
use Core\Component\Logger;
use Core\Http\Response;
use FastRoute\RouteCollector;

/**
 * 路由
 *
 * Class Router
 *
 * @package Jobs
 */
class Router extends ARouter
{
    function register(RouteCollector $routeCollector)
    {
//        $routeCollector->addRoute(['GET', 'POST'], "/router", function () {
//            $res = Response::getInstance();
//            $res->writeJson();
//            $res->end();
//        });
//        $routeCollector->addRoute("GET", "/router2", '/test');
    }
}