<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

use Core\Http\Request;
use Core\Http\Response;
use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

abstract class ARouter
{
    protected $isCache = false;
    protected $cacheFile;
    private $routeCollector;

    public function __construct()
    {
        $this->routeCollector = new RouteCollector(new Std(), new GroupCountBased());
        $this->register($this->routeCollector);
    }

    abstract public function register(RouteCollector $routeCollector);

    public function getRouteCollector()
    {
        return $this->routeCollector;
    }

    public function request()
    {
        return Request::getInstance();
    }

    public function response()
    {
        return Response::getInstance();
    }
}
