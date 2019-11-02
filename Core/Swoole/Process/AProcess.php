<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/17
 * Time: 上午11:28
 */

namespace Core\Swoole\Process;


use Core\Component\SysConst;
use Core\Swoole\Memory\TableManager;
use Core\Swoole\Server;
use swoole_process;

/**
 * Class AProcess
 *
 * @package Core\Swoole\Process
 */
abstract class AProcess
{
    private $_processName;
    private $_swooleProcess;
    private $_tableKey;
    private $_async = null;
    private $_args  = [];

    /**
     * AProcess constructor.
     *
     * @param string $processName
     * @param bool   $redirectStdinStdout 是否重定向标准输入输出
     * @param array  $args
     * @param bool   $async
     */
    function __construct($processName, $redirectStdinStdout = false, array $args = [], $async = true)
    {
        $this->_async         = $async;
        $this->_args          = $args;
        $this->_processName   = $processName;
        $this->_tableKey      = md5($processName);
        $this->_swooleProcess = new swoole_process([$this, '__start'], $redirectStdinStdout, 2);
        Server::getInstance()->getServer()->addProcess($this->_swooleProcess);
        if (\method_exists($this, 'initialize')) {
            $this->initialize($processName, $args, $async);
        }
    }

    /**
     * @return swoole_process
     */
    public function getProcess()
    {
        return $this->_swooleProcess;
    }

    /**
     * 服务启动后才能获得到 pid
     *
     * @return int|null
     */
    public function getPid()
    {
        if (isset($this->_swooleProcess->pid)) {
            return $this->_swooleProcess->pid;
        } else {
            $pid = TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP)->get($this->_tableKey);
            if ($pid) {
                return $pid['pid'];
            } else {
                return null;
            }
        }
    }

    /**
     * @return string
     */
    public function getProcessName()
    {
        return $this->_processName;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->_args;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    public function getArg($key)
    {
        if (isset($this->_args[$key])) {
            return $this->_args[$key];
        } else {
            return null;
        }
    }

    /**
     * @param swoole_process $process
     */
    function __start(swoole_process $process)
    {
        $processName = $this->getProcessName();
        if (PHP_OS != 'Darwin') {
            $process->name($processName);
        }
        TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP)->set(
            $this->_tableKey, ['pid' => $this->_swooleProcess->pid]
        );
        ProcessManager::getInstance()->setProcess($processName, $this);
        swoole_process::signal(SIGTERM, function () use ($process) {
            $this->onShutDown();
            TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP)->del($this->_tableKey);
            \swoole_event_del($process->pipe);
            $this->_swooleProcess->exit(0);
        });
        if ($this->_async) {
            \swoole_event_add($this->_swooleProcess->pipe, function () {
                $msg = $this->_swooleProcess->read(64 * 1024);
                $this->onReceive($msg);
            });
        }
        $this->run($process);
    }

    /**
     * @param swoole_process $worker
     */
    public abstract function run(swoole_process $worker);

    /**
     * @return mixed
     */
    public abstract function onShutDown();

    /**
     * @param       $str
     * @param mixed ...$args
     */
    public abstract function onReceive($str, ...$args);

}