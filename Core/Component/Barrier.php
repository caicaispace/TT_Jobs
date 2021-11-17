<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

use Core\Swoole\Server;

class Barrier
{
    private $tasks   = [];
    private $maps    = [];
    private $results = [];

    public function add($taskName, $callable)
    {
        if ($callable instanceof \Closure) {
            try {
                $callable = new SuperClosure($callable);
            } catch (\Exception $exception) {
                trigger_error('async task serialize fail ');
                return false;
            }
        }
        $this->tasks[$taskName] = $callable;
        return true;
    }

    public function run($timeout = 0.5)
    {
        $temp = [];
        foreach ($this->tasks as $name => $task) {
            $temp[]       = $task;
            $this->maps[] = $name;
        }
        if (! empty($temp)) {
            $ret = Server::getInstance()->getServer()->taskWaitMulti($temp, $timeout);
            if (! empty($ret)) {
                //极端情况下  所有任务都超时
                foreach ($ret as $index => $result) {
                    $this->results[$this->maps[$index]] = $result;
                }
            }
        }
        return $this->results;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getResult($taskName)
    {
        if (isset($this->results[$taskName])) {
            return $this->results[$taskName];
        }
        return null;
    }
}
