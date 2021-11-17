<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\Server;

class Service
{
    protected $actionRegisterClass;

    public function getActionRegisterClass()
    {
        return $this->actionRegisterClass;
    }

    public function setActionRegisterClass($actionRegisterClass)
    {
        $this->actionRegisterClass = $actionRegisterClass;
    }
}
