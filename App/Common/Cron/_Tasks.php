<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/18
 * Time: 3:45
 */

namespace Common\Cron;

use Core\Swoole\Memory\TableManager;
use Core\Component\Crontab\Parse;
use Core\Component\Logger;
use Core\Utility\SnowFlake;

class _Tasks
{
    const SWOOLE_TABLE_NAME = 'CRON_TASKS';
    const TASKS_SIZE = 1024;

    private $_table;

    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        TableManager::getInstance()->add(self::SWOOLE_TABLE_NAME, [
            'id'         => ['type' => \swoole_table::TYPE_STRING, 'size' => 11],
            'second'     => ['type' => \swoole_table::TYPE_STRING, 'size' => 12],
            'minute'     => ['type' => \swoole_table::TYPE_STRING, 'size' => 12],
            'run_id'     => ['type' => \swoole_table::TYPE_STRING, 'size' => 20],
            'run_status' => ['type' => \swoole_table::TYPE_STRING, 'size' => 2],
        ], 1024
        );
        $this->_table = TableManager::getInstance()->get(self::SWOOLE_TABLE_NAME);
    }

    /**
     * 获取当前可以执行的任务
     * @return array
     */
    public function getTasks()
    {
        $data = [];
        if ($this->_table->count() <= 0) {
            return [];
        }
        $minute = date("YmdHi");
        foreach ($this->_table as $k => $task) {
            if ($minute == $task["minute"]) {
                if (time() == $task["second"] && TasksLoad::RUN_STATUS_NORMAL == $task["run_status"]) {
                    $data[$k] = $task["id"];
                }
            }
        }
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $tableData = [
                    "run_status"     => TasksLoad::RUN_STATUS_START,
                    "run_time_start" => time()
                ];
                $this->_table->set($k, $tableData);
            }
        }
        return $data;
    }

    /**
     * 每分钟执行一次，判断下一分钟需要执行的任务
     * @return bool
     */
    public function checkTasks()
    {
        //清理完成任务
        $this->_clean();
        $tasks = TasksLoad::getInstance()->getTasks();
        if ($tasks->count() > 0) {
            $time = time();
            foreach ($tasks as $id => $task) {
                if ($task["status"] != TasksLoad::TASK_START) {
                    continue;
                }
                $ret = Parse::parse($task["cron_spec"], $time);
                if (false === $ret) {
                    Logger::getInstance()->log(Parse::$error);
                    continue;
                }
                if (!empty($ret)) {
                    $minute = date("YmdHi");
                    $time   = strtotime(date("Y-m-d H:i"));
                    foreach ($ret as $second) {
                        if ($this->_table->count() > self::TASKS_SIZE) {
                            Logger::getInstance()->log("checkTask fail ,because tasks size Max");
                            break;
                        }
                        $key = SnowFlake::make();
                        $this->_table->set($key, [
                            "id"         => $id,
                            "second"     => $time + $second,
                            "minute"     => $minute,
                            "run_status" => TasksLoad::RUN_STATUS_NORMAL
                        ]);
                    }
                }
            }
        }
        return true;
    }

    /**
     * 清理已执行过的任务
     */
    private function _clean()
    {
        $taskIds     = [];
        $LoadTaskIds = [];
        $loadTasks   = TasksLoad::getInstance()->getTasks();
        $count       = $this->_table->count();
        if ($count > 0) {
            $minute = date("YmdHi");
            foreach ($this->_table as $id => $task) {
                //以下状态,不需要在存储任务
                if (in_array($task["run_status"],
                    [
                        TasksLoad::RUN_STATUS_SUCCESS,
                        TasksLoad::RUN_STATUS_FAILED,
                        TasksLoad::RUN_STATUS_ERROR,
                        TasksLoad::RUN_STATUS_TO_TASK_FAILED
                    ]
                )) {
                    $taskIds[] = $id;
                    continue;
                }
                $info = $loadTasks->get($task["id"]);
                if (!is_array($info) || !array_key_exists("timeout", $info)) {
                    continue;
                }
                //如果运行中的任务超过了阈值,则把超过1个小时没有响应的任务清除
                if ($count > self::TASKS_SIZE && $task["run_status"] == TasksLoad::RUN_STATUS_TO_TASK_SUCCESS) {
                    if (intval($minute) > intval($task["minute"]) + 60) {
                        $taskIds[]     = $id;
                        $LoadTaskIds[] = $task["id"];
                        continue;
                    }
                }
                //如果该任务无超时设置,则不进行处理
                if ($info["timeout"] <= 0) {
                    continue;
                }
                //到了超时时间
                $timeout = intval($info["timeout"] / 60);
                $timeout = $timeout > 1 ? $timeout : 1;
                if (intval($minute) > intval($task["minute"]) + $timeout) {
                    $taskIds[] = $id;
                    if ($task["run_status"] == TasksLoad::RUN_STATUS_START
                        || $task["run_status"] == TasksLoad::RUN_STATUS_TO_TASK_SUCCESS
                        || $task["run_status"] == TasksLoad::RUN_STATUS_ERROR
                    ) {
                        $LoadTaskIds[] = $task["id"];
                    }
                }
            }
        }
        //删除
        foreach ($taskIds as $id) {
            $this->_table->del($id);
        }
        //超时则把运行中的数量-1
        foreach ($LoadTaskIds as $tid) {
            $loadTasks->decr($tid, "exec_count");
        }
    }
}