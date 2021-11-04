<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Dispatcher;

use Core\Component\Error\Trigger;
use Core\Swoole\Memory\TableManager;
use Core\Swoole\Server;

/**
 * Class ProcessManager.
 */
class ProcessManager
{
    public const SWOOLE_TABLE_NAME = 'JOBS_PROCESS_MANAGER';

    protected static $instance;

    private $_processList = [];

    private $_table;

    public function __construct()
    {
        TableManager::getInstance()->add(
            self::SWOOLE_TABLE_NAME,
            [
                'pid' => [
                    'type' => \swoole_table::TYPE_INT,
                    'size' => 10,
                ],
            ],
            256
        );
        $this->_table = TableManager::getInstance()->get(self::SWOOLE_TABLE_NAME);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param string $key
     * @param string $processName
     * @param string $processClass
     * @param callable $onFinish
     *
     * @return bool
     */
    public function addProcess($key, $processName, $processClass, $onFinish, array $args = [])
    {
        if (Server::getInstance()->isStart() === Server::SERVER_NOT_START) {
            trigger_error("you can't add a process {$processName}.{$processClass} after server start");
            return false;
        }
        $md5Key = $this->_generateKey($key);
        if (! isset($this->_processList[$md5Key])) {
            try {
                $this->_processList[$md5Key] = (new $processClass($key, $processName, $onFinish, $args));
                return true;
            } catch (\Throwable $throwable) {
                Trigger::error($throwable);
                return false;
            }
        } else {
            trigger_error("you can't add the same name process : {$processName}.{$processClass}");
            return false;
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function removeProcessByKey($key)
    {
        if ($process = $this->getProcessByKey($key)) {
            $pid = $process->getPid();
            $this->removeProcessByPid($pid);
        }
        return true;
    }

    /**
     * @param int $pid
     *
     * @return bool
     */
    public function removeProcessByPid($pid)
    {
        if ($process = $this->getProcessByPid($pid)) {
            $this->_removeInTable($process);
            if (\swoole_process::kill($pid, 0)) {
                \swoole_process::kill($pid);
                while ($ret = \swoole_process::wait(false));
//                    echo "PID={$ret['pid']}\n";
            }
        }
        return true;
    }

    /**
     * @param $key
     *
     * @return null|Process
     */
    public function getProcessByKey($key)
    {
        $key = $this->_generateKey($key);
        if (isset($this->_processList[$key])) {
            return $this->_processList[$key];
        }
        return null;
    }

    /**
     * @param int $pid
     *
     * @return null|Process
     */
    public function getProcessByPid($pid)
    {
        foreach ($this->_table as $key => $item) {
            if ($item['pid'] == $pid) {
                return $this->_processList[$key];
            }
        }
        return null;
    }

    /**
     * @param $key
     * @param $process
     */
    public function setProcess($key, $process)
    {
        $this->_processList[$this->_generateKey($key)] = $process;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function reboot($key)
    {
        if ($process = $this->getProcessByKey($key)) {
            \swoole_process::kill($process->getPid(), SIGTERM);
            return true;
        }
        return false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function kill($key)
    {
        if ($process = $this->getProcessByKey($key)) {
            $pid = $process->getPid();
            if (\swoole_process::kill($pid, 0)) {
                \swoole_process::kill($pid);
                while ($ret = \swoole_process::wait(false));
//                    echo "PID={$ret['pid']}\n";
            }
            $this->_removeInTable($process);
        }
        return true;
    }

    private function _removeInTable(Process $process)
    {
        $key = $this->_generateKey($process->getProcessKey());
        if ($this->_table->exist($key)) {
            $this->_table->del($key);
        }
        if (isset($this->_processList[$key])) {
            unset($this->_processList[$key]);
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function _generateKey($key)
    {
        return hash('md5', $key);
    }
}
