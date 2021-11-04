<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

use Core\Vendor\Tools\LogicRequest;
use Core\Vendor\Tools\LogicResponse;

abstract class ALogic
{
    protected $request;
    protected $response;

    public function __construct()
    {
        $this->request  = LogicRequest::getInstance();
        $this->response = LogicResponse::getInstance();
    }

    abstract public function getList();

    abstract public function getInfo();

    abstract public function create();

    abstract public function update();

    abstract public function delete();

    public function request()
    {
        return $this->request;
    }

    public function response()
    {
        return $this->response;
    }

    public function actionNotFound($actionName)
    {
        return $this->response()->error($actionName . ' method not found');
    }

    /**
     * @param string $actionName
     *
     * @return LogicResponse
     */
    public function call($actionName)
    {
        if (! method_exists($this, $actionName)) {
            return $this->actionNotFound($actionName);
        }
        $eventActions = ['getList', 'getInfo', 'create', 'update', 'delete'];
        if (\in_array($actionName, $eventActions, true)) {
            $eventBeforeActionName = '_EVENT_before' . ucfirst($actionName);
            if (method_exists($this, $eventBeforeActionName)) {
                $this->{$eventBeforeActionName}();
            }
        }
        $response = $this->{$actionName}();
        if (\in_array($actionName, $eventActions, true)) {
            $eventAfterActionName = '_EVENT_after' . ucfirst($actionName);
            if (method_exists($this, $eventAfterActionName)) {
                $this->{$eventAfterActionName}();
            }
        }
        return $response;
    }
}
