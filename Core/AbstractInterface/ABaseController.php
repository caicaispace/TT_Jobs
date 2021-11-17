<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

abstract class ABaseController
{
    protected $actionName;
    protected $callArgs;

    public function __call($actionName, $arguments)
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
        if (! $this->response()->isEndResponse()) {
            $realName = $this->actionName();
            if (method_exists($this, $realName)) {
                $this->{$realName}();
            } else {
                $this->actionNotFound($realName, $arguments);
            }
        }
        $this->afterAction();
    }

    public function actionName($actionName = null)
    {
        if ($actionName === null) {
            return $this->actionName;
        }
        $this->actionName = $actionName;
    }

    abstract public function index();

    abstract public function request();

    abstract public function response();

    abstract public function responseError();

    abstract protected function onRequest($actionName);

    abstract protected function actionNotFound($actionName = null, $arguments = null);

    abstract protected function afterAction();
}
