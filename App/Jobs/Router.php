<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs;

use Core\AbstractInterface\ARouter;
use Core\Http\Response;
use FastRoute\RouteCollector;

/**
 * 路由.
 *
 * Class Router
 */
class Router extends ARouter
{
    public function register(RouteCollector $routeCollector)
    {
//        $routeCollector->addRoute(['GET', 'POST'], "/router", function () {
//            $res = Response::getInstance();
//            $res->writeJson();
//            $res->end();
//        });
//        $routeCollector->addRoute("GET", "/router2", '/test');
    }
}
