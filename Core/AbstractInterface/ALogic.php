<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/16
 * Time: 19:02
 */

namespace Core\AbstractInterface;

use Core\Vendor\Tools\LogicResponse;
use Core\Vendor\Tools\LogicRequest;

abstract class ALogic
{

    protected $request;
    protected $response;

    function __construct()
    {
        $this->request  = LogicRequest::getInstance();
        $this->response = LogicResponse::getInstance();
    }

    abstract function getList();

    abstract function getInfo();

    abstract function create();

    abstract function update();

    abstract function delete();

    function request()
    {
        return $this->request;
    }

    function response()
    {
        return $this->response;
    }

    function actionNotFound($actionName)
    {
        return $this->response()->error($actionName . ' method not found');
    }

    /**
     * @param string $actionName
     *
     * @return LogicResponse
     */
    function call($actionName)
    {
        if (!method_exists($this, $actionName)) {
            return $this->actionNotFound($actionName);
        }
        $eventActions = ['getList', 'getInfo', 'create', 'update', 'delete'];
        if (\in_array($actionName, $eventActions, true)) {
            $eventBeforeActionName = '_EVENT_before' . ucfirst($actionName);
            if (method_exists($this, $eventBeforeActionName)) {
                $this->$eventBeforeActionName();
            }
        }
        $response = $this->$actionName();
        if (\in_array($actionName, $eventActions, true)) {
            $eventAfterActionName = '_EVENT_after' . ucfirst($actionName);
            if (method_exists($this, $eventAfterActionName)) {
                $this->$eventAfterActionName();
            }
        }
        return $response;
    }
}