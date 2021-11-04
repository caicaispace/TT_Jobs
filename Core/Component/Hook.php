<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

class Hook
{
    protected static $instance;
    private $eventList = [];

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function listen($event, callable $callback)
    {
        $this->eventList[$event] = $callback;
        return $this;
    }

    public function event($event, ...$arg)
    {
        if (isset($this->eventList[$event])) {
            $handler = $this->eventList[$event];
            try {
                call_user_func_array($handler, $arg);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
    }
}
