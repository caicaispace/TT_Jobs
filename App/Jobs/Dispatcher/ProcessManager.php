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

    private array $_processList = [];

    private $_table;

    public function __construct()
    {
        TableManager::getInstance()->add(
            self::SWOOLE_TABLE_NAME,
            [
                'pid' => [
                    'type' => \Swoole\Table::TYPE_INT,
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
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addProcess(string $key, string $processName, string $processClass, callable $onFinish, array $args = []): bool
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

    public function removeProcessByKey(string $key): bool
    {
        if ($process = $this->getProcessByKey($key)) {
            $pid = $process->getPid();
            $this->removeProcessByPid($pid);
        }
        return true;
    }

    public function removeProcessByPid(int $pid): bool
    {
        if ($process = $this->getProcessByPid($pid)) {
            $this->_removeInTable($process);
            if (\Swoole\Process::kill($pid, 0)) {
                \Swoole\Process::kill($pid);
                while ($ret = \Swoole\Process::wait(false));
//                    echo "PID={$ret['pid']}\n";
            }
        }
        return true;
    }

    public function getProcessByKey(string $key): ?Process
    {
        $key = $this->_generateKey($key);
        if (isset($this->_processList[$key])) {
            return $this->_processList[$key];
        }
        return null;
    }

    public function getProcessByPid(int $pid): ?Process
    {
        foreach ($this->_table as $key => $item) {
            if ($item['pid'] == $pid) {
                return $this->_processList[$key];
            }
        }
        return null;
    }

    public function setProcess(string $key, Process $process)
    {
        $this->_processList[$this->_generateKey($key)] = $process;
    }

    /**
     * @param $key
     */
    public function reboot(string $key): bool
    {
        if ($process = $this->getProcessByKey($key)) {
            \Swoole\Coroutine::resume($process->getPid());
            return true;
        }
        return false;
    }

    public function kill(string $key): bool
    {
        if ($process = $this->getProcessByKey($key)) {
            $pid = $process->getPid();
            if (\Swoole\Coroutine::exists($pid)) {
                \Swoole\Coroutine::cancel($pid);
                \Swoole\Coroutine\System::waitPid($pid);
                // echo "PID={$ret['pid']}\n";
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

    private function _generateKey(string $key): string
    {
        return hash('md5', $key);
    }
}
