<?php

namespace Core\AbstractInterface;


abstract class ABaseController
{
    protected $actionName = null;
    protected $callArgs   = null;

    function actionName($actionName = null)
    {
        if ($actionName === null) {
            return $this->actionName;
        } else {
            $this->actionName = $actionName;
        }
    }

    abstract function index();

    abstract protected function onRequest($actionName);

    abstract protected function actionNotFound($actionName = null, $arguments = null);

    abstract protected function afterAction();

    abstract function request();

    abstract function response();

    abstract function responseError();

    function __call($actionName, $arguments)
    {
        /*
           * 防止恶意调用
           * actionName、onRequest、actionNotFound、afterAction、request
           * response、__call
        */
        if (in_array($actionName, [
            'actionName', 'onRequest', 'actionNotFound', 'afterAction', 'request', 'response', '__call',
        ])) {
            $this->responseError();
            return;
        }
        //执行onRequest事件
        $this->actionName($actionName);
        $this->onRequest($actionName);
        //判断是否被拦截
        if (!$this->response()->isEndResponse()) {
            $realName = $this->actionName();
            if (method_exists($this, $realName)) {
                $this->$realName();
            } else {
                $this->actionNotFound($realName, $arguments);
            }
        }
        $this->afterAction();
    }
}