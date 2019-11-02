<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/19
 * Time: 18:46
 */

namespace Common\Cron;

use Common\Cron\TasksLoad as CronLoadTasks;
use Common\Cron\Tasks as CronTasks;
use Core\Swoole\Timer;
use swoole_server;

/**
 * 调度器
 * Class Dispatcher
 *
 * @package Common\Cron
 */
class Dispatcher
{
    /**
     * @var swoole_server
     */
    private $_server;

    /**
     * @var integer
     */
    private $_workerId;

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

    }

    /**
     * @param swoole_server $server
     * @param int           $workerId
     *
     * @return $this
     */
    function setServer(swoole_server $server, $workerId)
    {
        $this->_server   = $server;
        $this->_workerId = $workerId;
        return $this;
    }

    function dispatch()
    {
        if (!$this->_server || !$this->_workerId) {
            return;
        }
        if ($this->_workerId == 1) {
            $this->_checkTasks();
        }
        if ($this->_workerId == 2) {
            $this->_runTask();
        }
    }

    private function _checkTasks()
    {
        Timer::delay((60 - date("s")) * 1000, function () { /* 准点载入任务 */
            CronTasks::getInstance()->checkTasks();
            $this->_server->tick(60000, function () {
                CronTasks::getInstance()->checkTasks();
            });
        });
    }

    private function _runTask()
    {
        Timer::loop(1000, function () {
            $tasks = CronTasks::getInstance()->getTasks();
            if (empty($tasks)) {
                return;
            }
            foreach ($tasks as $task) {
                if (!$taskInfo = CronLoadTasks::getInstance()->getTasks()->get($task['id'])) {
                    continue;
                }
                if ($taskModel = (new \Cron\Model\Task)->get($taskInfo['id'])) {
                    $taskModel->setAttr('prev_time', time());
                    $taskModel->setAttr('execute_times', $taskModel->getAttr('execute_times') + 1);
                    $taskModel->save();
                }
                ProcessManager::getInstance()->addProcess(
                    $taskInfo['task_name'],
                    $taskInfo['command'],
                    Process::class,
                    [$this, '_taskOnFinish'],
                    $taskInfo
                );
            }
        });
    }

    function _taskOnFinish($result, $status, $taskInfo)
    {
//        echo '-------------' . date('Y-m-d H:i:s') . '-------------' . PHP_EOL;
//
//        echo 'result: ' . $result . PHP_EOL;
//        echo 'status code: ' . $status['code'] . PHP_EOL;
//        echo 'status signal: ' . $status['signal'] . PHP_EOL;
//        echo 'task_id: ' . $taskInfo['id'] . PHP_EOL;
//        echo 'task_name: ' . $taskInfo['task_name'] . PHP_EOL;
//        echo 'task_command: ' . $taskInfo['command'] . PHP_EOL;
//        var_dump($taskInfo);

        if ($task = TasksLoad::getInstance()->getTasks()->get($taskInfo['id'])) {
            $taskLogModel = new \Cron\Model\TaskLog;
            $taskLogModel->setAttr('task_id', $task['id']);
            $taskLogModel->setAttr('output', $result);
            $taskLogModel->setAttr('command', $task['command']);
            $taskLogModel->setAttr('process_time', time() - $task['run_time_update']);
            $taskLogModel->save();
            ProcessManager::getInstance()->removeProcessByKey($taskInfo['task_name']);
        }
    }
}