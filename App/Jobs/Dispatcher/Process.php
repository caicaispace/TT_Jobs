<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Dispatcher;

use Core\Swoole\Memory\TableManager;

/**
 * Class Process.
 */
class Process
{
    private string $_key;
    private int $_pid;
    private $_onFinish;
    private string $_processName;
    private array $_args = [];

    /**
     * Process constructor.
     */
    public function __construct(string $key, string $processName, callable $onFinish, array $args)
    {
        $gid = \Swoole\Coroutine::create(function () use ($processName) {
            $ret = \Swoole\Coroutine\System::exec($processName);
            if ($ret) {
                $this->onFinish($ret['output'], $ret);
            }
        });

        $this->_args        = $args;
        $this->_key         = $key;
        $this->_onFinish    = $onFinish;
        $this->_processName = $processName;
        $this->_pid         = $gid;

        TableManager::getInstance()->get(ProcessManager::SWOOLE_TABLE_NAME)->set(md5($key), ['pid' => $gid]);
    }

    public function getPid(): ?int
    {
        return $this->_pid;
    }

    public function getProcessKey(): string
    {
        return $this->_key;
    }

    public function getProcessName(): string
    {
        return $this->_processName;
    }

    public function getArgs(): array
    {
        return $this->_args;
    }

    public function getArg(string $key)
    {
        return isset($this->_args[$key])
            ? $this->_args[$key]
            : null;
    }

    public function onFinish(string $result, array $status)
    {
        call_user_func($this->_onFinish, $result, $status, $this->_args);
    }

    public function onReceive(string $str, ...$args)
    {
    }
}
