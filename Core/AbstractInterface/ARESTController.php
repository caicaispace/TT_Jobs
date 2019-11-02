<?php

namespace Core\AbstractInterface;

use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;
use Core\Vendor\Tools\HttpResponseJsonSchema;


abstract class ARESTController extends ABaseController
{
    /*
     * 支持方法
        'GET',      // 从服务器取出资源（一项或多项）
        'POST',     // 在服务器新建一个资源
        'PUT',      // 在服务器更新资源（客户端提供改变后的完整资源）
        'PATCH',    // 在服务器更新资源（客户端提供改变的属性）
        'DELETE',   // 从服务器删除资源
        'HEAD',     // 获取 head 元数据
        'OPTIONS',  // 获取信息，关于资源的哪些属性是客户端可以改变的
     */
    function index()
    {
        $this->actionNotFound();
    }

    function request()
    {
        $request = Request::getInstance();
        $request->setExtendSpecification(Request::REST_SPECIFICATION);
        return $request;
    }

    function response()
    {
        return Response::getInstance();
    }

    public function responseError()
    {
        return true;
    }

    function json()
    {
        return HttpResponseJsonSchema::getInstance();
    }

    function getPageData()
    {
        /* 分页 */
        $page    = $this->request()->getQueryParam('page');
        $limit   = $this->request()->getQueryParam('limit');
        $isFirst = $this->request()->getQueryParam('first');

        $pageParams = [
            'page'     => (int)$page,
            'limit'    => (int)$limit,
            'start'    => (int)($page - 1) * $limit,
            'is_first' => (int)$isFirst,
        ];

        return $pageParams;
    }

    protected function onRequest($actionName)
    {
    }

    protected function afterAction()
    {
    }

    protected function actionNotFound($actionName = null, $arguments = null)
    {
        $this->response()->withStatus(Status::CODE_NOT_FOUND);
    }

    function __call($actionName, $arguments)
    {
        /*
         * restful中无需预防恶意调用控制器内置方法。
         */
        $actionName = $this->request()->getMethod() . '_' . lcfirst($actionName);
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