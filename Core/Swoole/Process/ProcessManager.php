<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/11
 * Time: 下午9:00
 */

namespace Core\Swoole\Process;


use Core\Component\SysConst;
use Core\Component\Error\Trigger;
use Core\Swoole\Memory\TableManager;
use Core\Swoole\Server;

/**
 * Class ProcessManager
 *
 * @package Core\Swoole\Process
 */
class ProcessManager
{
    private $_processList = [];

    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function __construct()
    {
        TableManager::getInstance()->add(
            SysConst::PROCESS_HASH_MAP, [
            'pid' => [
                'type' => \swoole_table::TYPE_INT,
                'size' => 10,
            ],
        ], 256
        );
    }

    /**
     * @param string $processName
     * @param string $processClass
     * @param bool   $redirectStdinStdout
     * @param array  $args
     * @param bool   $async
     *
     * @return bool
     */
    public function addProcess($processName, $processClass, $redirectStdinStdout = false, array $args = [], $async = true)
    {
        if (Server::SERVER_STARTED === Server::getInstance()->isStart()) {
            trigger_error("you can not add a process {$processName}.{$processClass} after server start");
            return false;
        }
        $key = md5($processName);
        if (!isset($this->_processList[$key])) {
            try {
                $process                  = new $processClass($processName, $redirectStdinStdout, $args, $async);
                $this->_processList[$key] = $process;
                return true;
            } catch (\Throwable $throwable) {
                Trigger::error($throwable);
                return false;
            }
        } else {
            trigger_error("you can not add the same name process : {$processName}.{$processClass}");
            return false;
        }
    }

    /**
     * @param string $processName
     *
     * @return bool
     */
    public function removeProcessByName($processName)
    {
        if ($process = $this->getProcessByName($processName)) {
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
                $process->getProcess()->exit(0);
                while ($ret = \swoole_process::wait(false)) {
//                    echo "PID={$ret['pid']}\n";
                }
            }
        }
        return true;
    }

    /**
     * @param string $processName
     *
     * @return AProcess|null
     */
    public function getProcessByName($processName)
    {
        $key = md5($processName);
        if (isset($this->_processList[$key])) {
            return $this->_processList[$key];
        } else {
            return null;
        }
    }

    /**
     * @param int $pid
     *
     * @return AProcess|null
     */
    public function getProcessByPid($pid)
    {
        $table = TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP);
        foreach ($table as $key => $item) {
            if ($item['pid'] == $pid) {
                return $this->_processList[$key];
            }
        }
        return null;
    }

    /**
     * @param string $processName
     * @param        $process
     */
    public function setProcess($processName, $process)
    {
        $key                      = md5($processName);
        $this->_processList[$key] = $process;
    }

    /**
     * @param string $processName
     *
     * @return bool
     */
    public function reboot($processName)
    {
        if ($process = $this->getProcessByName($processName)) {
            \swoole_process::kill($process->getPid(), SIGTERM);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $processName
     *
     * @return bool
     */
    public function kill($processName)
    {
        if ($process = $this->getProcessByName($processName)) {
            $pid = $process->getPid();
            if (\swoole_process::kill($pid, 0)) {
                \swoole_process::kill($pid);
                while ($ret = \swoole_process::wait(false)) {
//                    echo "PID={$ret['pid']}\n";
                }
            }
            $this->_removeInTable($process);
        }
        return true;
    }

    /**
     * @param string $name
     * @param mixed  $data
     *
     * @return bool
     */
    public function writeByProcessName($name, $data)
    {
        if ($process = $this->getProcessByName($name)) {
            return (bool)$process->getProcess()->write($data);
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @param float  $timeOut
     *
     * @return null|string
     */
    public function readByProcessName($name, $timeOut = 0.1)
    {
        if ($process = $this->getProcessByName($name)) {
            $process = $process->getProcess();
            $read    = [$process];
            $write   = [];
            $error   = [];
            $ret     = \swoole_client_select($read, $write, $error, $timeOut);
            if ($ret) {
                return $process->read(64 * 1024);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * @param AProcess $process
     */
    private function _removeInTable(AProcess $process)
    {
        $key = md5($process->getProcessName());
        if (TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP)->exist($key)) {
            TableManager::getInstance()->get(SysConst::PROCESS_HASH_MAP)->del($key);
        }
        if (isset($this->_processList[$key])) {
            unset($this->_processList[$key]);
        }
    }
}