<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/1/23
 * Time: 上午12:44
 */

namespace Core\Http;


use Core\Conf\Config;
use Core\Conf\Event;
use Core\AbstractInterface\ABaseController as Controller;
use Core\AbstractInterface\ARouter;
use Core\Component\Di;
use Core\Component\SysConst;
use Core\Http\Message\Status;
use FastRoute\Dispatcher\GroupCountBased;

class Dispatcher
{
    protected static $selfInstance;
    protected $fastRouterDispatcher;
    protected $controllerPool = [];
    protected $useControllerPool = FALSE;
    protected $controllerMap = [];
    protected $serverParamMap = [];

    static function getInstance()
    {
        if (!isset(self::$selfInstance)) {
            self::$selfInstance = new Dispatcher();
        }
        return self::$selfInstance;
    }

    function __construct()
    {
        $this->useControllerPool = Config::getInstance()->getConf("CONTROLLER_POOL");
    }

    function dispatch()
    {
        if (Response::getInstance()->isEndResponse()) {
            return;
        }
        $httpMethod = Request::getInstance()->getMethod();
        $pathInfo  = UrlParser::pathInfo();
        $routeInfo = $this->doFastRouter($pathInfo, $httpMethod);
        if ($routeInfo !== FALSE) {
            switch ($routeInfo[0]) {
                case \FastRoute\Dispatcher::NOT_FOUND:
                    // ... 404 NdoDispatcherot Found
                    break;
                case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                    Response::getInstance()->withStatus(Status::CODE_METHOD_NOT_ALLOWED);
                    break;
                case \FastRoute\Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars    = $routeInfo[2];
                    if (is_callable($handler)) {
                        call_user_func_array($handler, $vars);
                    } else if (is_string($handler)) {
                        $data = Request::getInstance()->getRequestParam();
                        Request::getInstance()->withQueryParams($vars + $data);
                        $pathInfo = UrlParser::pathInfo($handler);
                        Request::getInstance()->getUri()->withPath($pathInfo);
                    }
                    break;
            }
        }
        if (Response::getInstance()->isEndResponse()) {
            return;
        }
        //去除为fastRouter预留的左边斜杠
        $pathInfo = ltrim($pathInfo, "/");
        if (isset($this->controllerMap[$pathInfo])) {
            $finalClass = $this->controllerMap[$pathInfo]['finalClass'];
            $actionName = $this->controllerMap[$pathInfo]['actionName'];
            if (isset($this->serverParamMap[$pathInfo]['id'])) {
                $actionName = $httpMethod == 'GET' ? 'info' : 'index';
                Request::getInstance()->setServerParam('id', $this->serverParamMap[$pathInfo]['id']);
            }
        } else {
            /*
             * 此处用于防止URL恶意攻击，造成Dispatch缓存爆满。
             */
            if (count($this->controllerMap) > 50000) {
                $this->controllerMap = [];
            }
            $list               = explode("/", $pathInfo);
            $controllerPath     = APP_NAME . "\\Controller";
            $appName            = null;
            $controllerName     = null;
            $actionName         = null;
            $finalClass         = null;
            $controllerMaxDepth = Di::getInstance()->get(SysConst::CONTROLLER_MAX_DEPTH);
            if (intval($controllerMaxDepth) <= 0) {
                $controllerMaxDepth = 3;
            }
            $maxDepth = count($list) < $controllerMaxDepth ? count($list) : $controllerMaxDepth;
            while ($maxDepth > 0) {
                $className = '';
                for ($i = 0; $i < $maxDepth; $i++) {
                    if (strstr($list[$i], '_')) {
                        $words    = explode('_', $list[$i]);
                        $list[$i] = '';
                        foreach ($words as $k => $v) {
                            $list[$i] .= ucfirst($v);
                        }
                    }
                    if (is_numeric(end($list))) {
                        $this->serverParamMap[$pathInfo] = ['id' => (int)array_pop($list)];
                        if ($httpMethod == 'GET' ) {
                            $actionName = 'info';
                        }
                        Request::getInstance()->setServerParam('id', $this->serverParamMap[$pathInfo]['id']);
                    }
                    $controllerName = ucfirst($list[$i]);
                    $className      = $className . "\\" . $controllerName;//为一级控制器Index服务
                }
                if (class_exists($controllerPath . $className)) {
                    //尝试获取该class后的actionName
                    if (NULL === $actionName) {
                        $actionName = empty($list[$i]) ? 'index' : $list[$i];
                    }
                    $finalClass = $controllerPath . $className;
                    break;
                } else {
                    //尝试搜搜index控制器
                    $controllerName = 'Index';
                    $temp           = $className . "\\" . $controllerName;
                    if (class_exists($controllerPath . $temp)) {
                        $finalClass = $controllerPath . $temp;
                        //尝试获取该class后的actionName
                        $actionName = empty($list[$i]) ? 'index' : $list[$i];
                        break;
                    }
                }
                $maxDepth--;
            }
            if (empty($finalClass)) {
                //若无法匹配完整控制器   搜搜Index控制器是否存在
                $controllerName = 'Index';
                $finalClass     = $controllerPath . "\\" . $controllerName;
                $actionName     = empty($list[0]) ? 'index' : $list[0];
            }
            $this->controllerMap[$pathInfo]['finalClass'] = $finalClass;
            $this->controllerMap[$pathInfo]['actionName'] = $actionName;
            $this->controllerMap[$pathInfo]['httpMethod'] = $httpMethod;
        }
        if (class_exists($finalClass)) {
            if ($this->useControllerPool) {
                if (isset($this->controllerPool[$finalClass])) {
                    $controller = $this->controllerPool[$finalClass];
                } else {
                    $controller                        = new $finalClass;
                    $this->controllerPool[$finalClass] = $controller;
                }
            } else {
                $controller = new $finalClass;
            }
            if ($controller instanceof Controller) {
                Event::getInstance()->onDispatcher(Request::getInstance(), Response::getInstance(), $finalClass, $actionName);
                if (Status::CODE_FORBIDDEN === Response::getInstance()->getStatusCode()) { // 用作权限验证
                    Response::getInstance()->withStatus(Status::CODE_OK);
                    return;
                }
                //预防在进控制器之前已经被拦截处理
                if (!Response::getInstance()->isEndResponse()) {
                    $controller->__call($actionName, null);
                }
            } else {
                Response::getInstance()->withStatus(Status::CODE_NOT_FOUND);
                trigger_error("controller {$finalClass} is not a instance of ABaseController");
            }
        } else {
            Response::getInstance()->withStatus(Status::CODE_NOT_FOUND);
            trigger_error("default controller Index not implement");
        }
    }

    private function doFastRouter($pathInfo, $requestMethod)
    {
        if (!isset($this->fastRouterDispatcher)) {
            $this->intRouterInstance();
        }
        if ($this->fastRouterDispatcher instanceof GroupCountBased) {
            return $this->fastRouterDispatcher->dispatch($requestMethod, $pathInfo);
        } else {
            return FALSE;
        }
    }

    private function intRouterInstance()
    {
        try {
            /*
             * if exit Router class in App directory
             */
            $ref    = new \ReflectionClass(APP_NAME . "\\Router");
            $router = $ref->newInstance();
            if ($router instanceof ARouter) {
                $this->fastRouterDispatcher = new GroupCountBased($router->getRouteCollector()->getData());
            }
        } catch (\Exception $exception) {
            //没有设置路由
            $this->fastRouterDispatcher = FALSE;
        }
    }

}