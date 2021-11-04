<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Version;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;

class Version
{
    private $versionName;
    private $judge;
    private $routeCollector;
    private $dispatcher;
    private $defaultHandler;

    public function __construct($versionName, callable $judge)
    {
        $this->versionName    = $versionName;
        $this->judge          = $judge;
        $this->routeCollector = new RouteCollector(new Std(), new GroupCountBased());
    }

    public function register()
    {
        return $this->routeCollector;
    }

    public function dispatch($urlPath, $requestMethod)
    {
        if ($this->dispatcher == null) {
            $this->dispatcher = new Dispatcher($this->routeCollector->getData());
        }
        return $this->dispatcher->dispatch($urlPath, $requestMethod);
    }

    /**
     * @return mixed
     */
    public function getVersionName()
    {
        return $this->versionName;
    }

    /**
     * @return callable
     */
    public function getJudge()
    {
        return $this->judge;
    }

    public function getDefaultHandler()
    {
        return $this->defaultHandler;
    }
}
