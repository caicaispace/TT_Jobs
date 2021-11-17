<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Socket\Common;

class CommandList
{
    protected $list = [];

    public function addCommandHandler($commandStr, callable $callback)
    {
        $this->list[$commandStr] = $callback;
    }

    public function setDefaultHandler(callable $callback)
    {
        $this->list['DEFAULT_HANDLER'] = $callback;
    }

    public function getHandler(Command $command)
    {
        $name = $command->getCommand();
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        if (isset($this->list['DEFAULT_HANDLER'])) {
            return $this->list['DEFAULT_HANDLER'];
        }
        return null;
    }
}
