<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Dispatcher;

use App\Jobs\Model\Task as TaskModel;
use Core\Component\Error\Trigger;
use Core\Swoole\Memory\TableManager;
use swoole_table;

/**
 * Class TasksLoad.
 */
class TasksLoad
{
    public const SWOOLE_TABLE_NAME = 'JOBS_LOAD_TASKS';

    public const LOAD_SIZE = 10240;

    public const TASK_START = 1;   // 正常
    public const TASK_STOP  = 0;   // 暂停

    public const RUN_STATUS_ERROR           = -1;  // 不符合条件，不运行
    public const RUN_STATUS_NORMAL          = 0;   // 未运行
    public const RUN_STATUS_START           = 1;   // 准备运行
    public const RUN_STATUS_TO_TASK_SUCCESS = 2;   // 发送任务成功
    public const RUN_STATUS_TO_TASK_FAILED  = 3;   // 发送任务失败
    public const RUN_STATUS_SUCCESS         = 4;   // 运行成功
    public const RUN_STATUS_FAILED          = 5;   // 运行失败

    protected static $instance;

    private $_table;

    private $_tableColumns = [
        'id'              => ['type' => swoole_table::TYPE_INT, 'size' => 11],
        'task_name'       => ['type' => swoole_table::TYPE_STRING, 'size' => 500],
        'cron_spec'       => ['type' => swoole_table::TYPE_STRING, 'size' => 500],
        'group_id'        => ['type' => swoole_table::TYPE_INT, 'size' => 11],
        'single'          => ['type' => swoole_table::TYPE_INT, 'size' => 1],
        'timeout'         => ['type' => swoole_table::TYPE_INT, 'size' => 11],
        'status'          => ['type' => swoole_table::TYPE_INT, 'size' => 2],
        'command'         => ['type' => swoole_table::TYPE_STRING, 'size' => 500],
        'exec_count'      => ['type' => swoole_table::TYPE_INT, 'size' => 8],
        'run_status'      => ['type' => swoole_table::TYPE_INT, 'size' => 2],
        'run_time_start'  => ['type' => swoole_table::TYPE_INT, 'size' => 11],
        'run_time_update' => ['type' => swoole_table::TYPE_INT, 'size' => 11],
    ];

    public function __construct()
    {
        TableManager::getInstance()->add(self::SWOOLE_TABLE_NAME, $this->_tableColumns, self::LOAD_SIZE);
        $this->_table = TableManager::getInstance()->get(self::SWOOLE_TABLE_NAME);
        $this->_loadTasks();
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @return bool
     */
    public function addTask(array $data)
    {
        if ($this->_table->count() > self::LOAD_SIZE) {
            return false;
        }

        $tableData = [
            'id'        => $data['id'],
            'task_name' => $data['task_name'],
            'cron_spec' => $data['cron_spec'],
            'group_id'  => $data['group_id'],
            'single'    => $data['single'],
            'timeout'   => $data['timeout'],
            'status'    => $data['status'],
            'command'   => $data['command'],
        ];

        return $this->_table->set($data['id'], $tableData);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function deleteTask($key)
    {
        return $this->_table->del($key);
    }

    /**
     * @param $key
     *
     * @return array
     */
    public function getTaskInfo($key)
    {
        return $this->_table->get($key);
    }

    /**
     * @return null|swoole_table
     */
    public function getTasks()
    {
        return $this->_table;
    }

    /**
     * @return bool
     */
    private function _loadTasks()
    {
        try {
            $model = new TaskModel();
//            $model = $model->where('status', 1);
            $ret   = $model->select();
            $tasks = $ret->toArray();
            foreach ($tasks as $task) {
                $this->addTask($task);
            }
        } catch (\Exception $e) {
            Trigger::exception($e);
        }
        return true;
    }
}
