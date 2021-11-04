<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Version;

use Core\Http\Request;
use Core\Http\UrlParser;
use FastRoute\Dispatcher;

class Controller
{
    private static $instance;
    private $versionList;

    public function __construct($versionRegisterClass)
    {
        $obj = new $versionRegisterClass();
        if ($obj instanceof ARegister) {
            $this->versionList = new VersionList();
            $obj->register($this->versionList);
        } else {
            throw new \Exception("{$versionRegisterClass} is not a valid class of AbstractRegister");
        }
    }

    public static function getInstance($versionRegisterClass)
    {
        if (! isset(self::$instance)) {
            self::$instance = new static($versionRegisterClass);
        }
        return self::$instance;
    }

    public function startController()
    {
        $list   = $this->versionList->all();
        $path   = UrlParser::pathInfo();
        $method = Request::getInstance()->getMethod();
        foreach ($list as $version) {
            if ($version instanceof Version) {
                $judge = $version->getJudge();
                //当当前的版本号判断器返回true时
                if (call_user_func($judge)) {
                    $routeInfo = $version->dispatch($path, $method);
                    if ($routeInfo !== false) {
                        switch ($routeInfo[0]) {
                            case Dispatcher::FOUND:
                                $handler = $routeInfo[1];
                                $vars    = $routeInfo[2];
                                if (is_callable($handler)) {
                                    call_user_func_array($handler, $vars);
                                } elseif (is_string($handler)) {
                                    $data = Request::getInstance()->getRequestParam();
                                    Request::getInstance()->withQueryParams($vars + $data);
                                    $pathInfo = UrlParser::pathInfo($handler);
                                    Request::getInstance()->getUri()->withPath($pathInfo);
                                }
                                break;
                        }
                    }
                    break;
                }
            }
        }
    }
}
