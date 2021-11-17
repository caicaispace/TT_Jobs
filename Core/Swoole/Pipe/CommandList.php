<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Pipe;

class CommandList
{
    private $list = [];

    public function add($command, callable $handler)
    {
        $this->list[$command] = $handler;
        return $this;
    }

    public function setDefaultHandler(callable $handler)
    {
        $this->list['__DEFAULT__'] = $handler;
        return $this;
    }

    public function getHandler($command)
    {
        if (isset($this->list[$command])) {
            return $this->list[$command];
        }
        if (isset($this->list['__DEFAULT__'])) {
            return $this->list['__DEFAULT__'];
        }
        return null;
    }
}
