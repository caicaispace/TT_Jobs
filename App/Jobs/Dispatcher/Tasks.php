<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/18
 * Time: 3:45
 */

namespace App\Jobs\Dispatcher;

use Core\Component\Error\Trigger;
use Core\Swoole\Memory\TableManager;
use Core\Component\Logger;
use Core\Utility\SnowFlake;
use Cron\CronExpression;

/**
 *
 * Class Tasks
 *
 * @package Jobs\Dispatcher
 */
class Tasks
{
    const SWOOLE_TABLE_NAME = 'JOBS_TASKS';
    const TASKS_SIZE        = 10240;

    private $_table;

    private $_tableColumns = [
        'id'              => ['type' => \swoole_table::TYPE_STRING, 'size' => 11],
        'run_id'          => ['type' => \swoole_table::TYPE_STRING, 'size' => 20],
        'run_minute'      => ['type' => \swoole_table::TYPE_STRING, 'size' => 12],
        'run_status'      => ['type' => \swoole_table::TYPE_INT, 'size' => 2],
        'run_time_start'  => ['type' => \swoole_table::TYPE_INT, 'size' => 11],
        "run_time_update" => ['type' => \swoole_table::TYPE_INT, 'size' => 11],
    ];

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
        TableManager::getInstance()->add(self::SWOOLE_TABLE_NAME, $this->_tableColumns, self::TASKS_SIZE);
        $this->_table = TableManager::getInstance()->get(self::SWOOLE_TABLE_NAME);
    }

    /**
     * 获取当前可以执行的任务
     *
     * @return array
     */
    function getTasks()
    {
        $data = [];
        if ($this->_table->count() <= 0) {
            return [];
        }
        $nowTime   = time();
        $nowMinute = date("YmdHi");
        foreach ($this->_table as $k => $task) {
            if (($nowMinute == $task["run_minute"]) && (TasksLoad::RUN_STATUS_NORMAL == $task["run_status"])) {
                $data[$k] = [
                    "id"              => $task['id'],
                    "run_status"      => TasksLoad::RUN_STATUS_START,
                    "run_time_update" => $nowTime,
                ];
                if (!isset($task['run_time_start'])) {
                    $data['run_time_start'] = $nowTime;
                }
            }
        }
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $this->_table->set($k, $v);
                TasksLoad::getInstance()->getTasks()->set($v['id'], $v);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     */
    function saveTask(array $data)
    {
        TasksLoad::getInstance()->addTask($data);
    }

    /**
     * @param $id
     */
    function deleteTask($id)
    {
        $name = TasksLoad::getInstance()->getTaskInfo($id)['task_name'];
        ProcessManager::getInstance()->removeProcessByKey($name);
        TasksLoad::getInstance()->deleteTask($id);
    }

    /**
     * 每分钟执行一次，判断下一分钟需要执行的任务
     *
     * @return bool
     */
    function checkTasks()
    {
        $this->_cleanTasks();
        $this->_cleanProcess();
        $tasks = TasksLoad::getInstance()->getTasks();
        if ($tasks->count() > 0) {
            foreach ($tasks as $id => $task) {
                if ($task["status"] == TasksLoad::TASK_STOP) {
                    continue;
                }
                try {
                    $jobs = CronExpression::factory($task["cron_spec"]);
                } catch (\Exception $e) {
                    Trigger::exception($e);
                    continue;
                }
                $runMinute = $jobs->getNextRunDate()->format('YmdHi');
                $runTime   = $jobs->getNextRunDate()->getTimestamp();
                $nowTime   = time();
                if (($runTime - $nowTime) > 60) {
                    continue;
                }
                if ($this->_table->count() > self::TASKS_SIZE) {
                    Logger::getInstance()->log("checkTask fail ,because tasks size Max");
                    break;
                }
                $tableData = [
                    "id"         => $id,
                    "run_minute" => $runMinute,
                    "run_status" => TasksLoad::RUN_STATUS_NORMAL,
                ];
                $this->_table->set(SnowFlake::make(), $tableData);
            }
        }
        return true;
    }

    /**
     * 清理完成任务
     */
    private function _cleanTasks()
    {
        $taskIds     = [];
        $LoadTaskIds = [];
        $loadTasks   = TasksLoad::getInstance()->getTasks();

        $count = $this->_table->count();
        if ($count > 0) {
            $minute = date("YmdHi");
            foreach ($this->_table as $id => $task) {
                if (in_array($task["run_status"],
                    [
                        TasksLoad::RUN_STATUS_SUCCESS,
                        TasksLoad::RUN_STATUS_FAILED,
                        TasksLoad::RUN_STATUS_ERROR,
                        TasksLoad::RUN_STATUS_TO_TASK_FAILED,
                    ]
                )) {
                    $taskIds[] = $id;
                    continue;
                }
                $info = $loadTasks->get($task["id"]);
                if (!is_array($info) || !array_key_exists("timeout", $info)) {
                    continue;
                }
                // 如果运行中的任务超过了阈值,则把超过1个小时没有响应的任务清除
                if ($count > self::TASKS_SIZE && $task["run_status"] == TasksLoad::RUN_STATUS_TO_TASK_SUCCESS) {
                    if (intval($minute) > intval($task["run_minute"]) + 60) {
                        $taskIds[]     = $id;
                        $LoadTaskIds[] = $task["id"];
                        continue;
                    }
                }
                // 如果该任务无超时设置,则不进行处理
                if ($info["timeout"] <= 0) {
                    continue;
                }
                // 到了超时时间
                $timeout = intval($info["timeout"] / 60);
                $timeout = $timeout > 1 ? $timeout : 1;
                if (intval($minute) > intval($task["run_minute"]) + $timeout) {
                    $taskIds[] = $id;
                    if ($task["run_status"] == TasksLoad::RUN_STATUS_START
                        or $task["run_status"] == TasksLoad::RUN_STATUS_TO_TASK_SUCCESS
                        or $task["run_status"] == TasksLoad::RUN_STATUS_ERROR
                    ) {
                        $LoadTaskIds[] = $task["id"];
                    }
                }
            }
        }
        // 删除
        foreach ($taskIds as $id) {
            $this->_table->del($id);
        }
        // 超时则把运行中的数量-1
        foreach ($LoadTaskIds as $tid) {
            $loadTasks->decr($tid, "exec_count");
        }
    }

    /**
     * 清理 process
     */
    private function _cleanProcess()
    {
        while ($ret = \swoole_process::wait(true)) {
            echo "swoole_process wait PID={$ret['pid']}" . PHP_EOL;
            ProcessManager::getInstance()->removeProcessByPid($ret['pid']);
        }
    }
}