<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\Client;

use Core\Component\RPC\Common\Package;

class CallList
{
    private $taskList = [];

    public function addCall($serverName, $action, array $args = null, callable $successCall = null, callable $failCall = null)
    {
        $package = new Package();
        $package->setServerName($serverName);
        $package->setAction($action);
        $package->setArgs($args);
        $this->taskList[] = new Call($package, $successCall, $failCall);
        return $this;
    }

    public function getTaskList()
    {
        return $this->taskList;
    }
}
