<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\Common;

class ActionList
{
    private $list = [];

    public function registerAction($name, callable $call)
    {
        $this->list[$name] = $call;
    }

    public function setDefaultAction(callable $call)
    {
        $this->list['__DEFAULT__'] = $call;
    }

    public function getHandler($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return isset($this->list['__DEFAULT__']) ? $this->list['__DEFAULT__'] : null;
    }
}
