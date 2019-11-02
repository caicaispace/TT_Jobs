<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/13
 * Time: 10:16
 */

namespace Common\Cron;

use Core\Swoole\Process\AProcess;
use Core\Swoole\Memory\TableManager;
use Core\Swoole\Process\ProcessManager;
use Core\Swoole\Timer;
use Cron\Logic\Task as TaskLogic;

/**
 * Class ParseProcess
 * @package Common\Cron
 */
class _ParseProcess extends AProcess
{
    const TABLE_NAME = 'CRON_PARSE_PROCESS';

    /**
     * @var \swoole_table
     */
    private $_table;

    /**
     * @var bool
     */
    private $_isFirst = false;

    public function initialize($processName, $args, $async)
    {
        TableManager::getInstance()->add(
            self::TABLE_NAME, [
                'command' => [
                    'type' => \swoole_table::TYPE_STRING,
                    'size' => 256
                ],
                'id' => [
                    'type' => \swoole_table::TYPE_INT,
                    'size' => 11
                ]
            ], 1024
        );
        $this->_table = TableManager::getInstance()->get(self::TABLE_NAME);
    }

    /**
     * @param \swoole_process $process
     */
    public function run(\swoole_process $process)
    {
        $this->_addToTable();
        $this->_handle();
    }

    private function _addToTable()
    {
        $callback = function () {
            if ($this->_table->count() <= 0) {
                $logic = new TaskLogic;
                $logic->request()->setWhere(['status = 1']);
                if ($list = $logic->getList()->getData()) {
                    foreach ($list as $k=>$v) {
                        $key = $v['id'] = intval($v['id']);
                        if (false === $this->_table->exist($key)){
                            $this->_table->set($key, $v);
                            // TODO 解析 crontab 时间表达式并执行任务
                        }
                    }
                }
            }
        };
        if (false === $this->_isFirst) {
            $callback();
            $this->_isFirst = true;
            $this->_addToTable();
        } else {
            Timer::loop(10000, $callback);
        }
    }

    private function _handle()
    {
        $callback = function () {
            foreach ($this->_table as $k=>$v) {
//                $processName = 'swoole_cron_handle_' . $v['id'];
                $processName = $v['command'];
                $process = ProcessManager::getInstance()->getProcessByKey($processName);
                if (null === $process) {
                    ProcessManager::getInstance()->addProcess($processName, HandleProcess::class, true);
                    $swooleProcess = ProcessManager::getInstance()->getProcessByKey($processName)->getProcess();
                    $swooleProcess->start();
                } else {
                    if (false === \swoole_process::kill($process->getPid(), 0)) {
                        ProcessManager::getInstance()->removeProcessByKey($processName);
                    }
                    while($ret = \swoole_process::wait(true)) {
//                    echo "PID={$ret['pid']}\n";
                    }
                    ProcessManager::getInstance()->writeByProcessName($processName, '--------->');
                    $msg = ProcessManager::getInstance()->readByProcessName($processName);
                    var_dump($msg);
                }
            }
        };
        Timer::loop(1000, $callback);
    }

    public function onShutDown()
    {
        echo 'process shut down';
    }

    /**
     * @param             $str
     * @param array|mixed ...$args
     */
    public function onReceive($str, ...$args)
    {
        echo get_class($this) . ':process rec: ' . $str . PHP_EOL;
    }
}