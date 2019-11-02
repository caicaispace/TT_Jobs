<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/19
 * Time: 18:46
 */

namespace Common\Cron;

use Core\Conf\Config;
use Core\Swoole\Process\ProcessManager;
use Common\Cron\ParseProcess as CronParseProcess;
use Common\Cron\TasksLoad as CronLoadTasks;
use Common\Cron\Tasks as CronTasks;
use Common\Cron\HandleProcess as CronHandleProcess;

/**
 * Class Dispatcher
 * @package Common\Cron
 */
class _Dispatcher
{
    /**
     * @var \swoole_server
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

    public function __construct()
    {

    }

    public function setServer(\swoole_server $server, $workerId)
    {
//        $server->on('pipeMessage', function($server, $src_worker_id, $data) {
//            echo "#{$server->worker_id} message from #$src_worker_id: $data\n";
//        });
        $this->_server = $server;
        $this->_workerId = $workerId;
        return $this;
    }

    public function dispatch()
    {
        if (!$this->_server || !$this->_workerId) {
            return false;
        }
        $checkTask = function () {
            CronTasks::getInstance()->checkTasks();
        };
        $workerNum = Config::getInstance()->getConf('SWOOLE.CONFIG.worker_num');
        if ($this->_workerId == 0) {
            /* 准点载入任务 */
            $this->_server->after((60 - date("s")) * 1000, function () use ($checkTask) {
                $checkTask();
                $this->_server->tick(60000, function () use ($checkTask) {
                    $checkTask();
                });
            });
        }

        $runTask = function ($task) {

//            $processName = $task['command'];
//            $pid = \swoole_async::exec($processName, function ($result, $status) {
//                var_dump($result, $status);
//            });
//            while ($ret = \swoole_process::wait(false)) {
//                $pid    = $ret['pid'];
//                var_dump($pid);
//            }
//            return;

            $processName = $task['command'];
            $process = ProcessManager::getInstance()->getProcessByKey($processName);

            if ($task['single'] == 0 && $process) {
                ProcessManager::getInstance()->removeProcessByKey($processName);
                $process = null;
            }

            $task['worker_id'] = $this->_workerId;
            if (null === $process) {
                ProcessManager::getInstance()->addProcess($processName, CronHandleProcess::class, true, $task);
                $swooleProcess = ProcessManager::getInstance()->getProcessByKey($processName)->getProcess();
                $swooleProcess->start();
//                \swoole_event_add($swooleProcess->pipe, function () use($swooleProcess) {
//                    $msg = $swooleProcess->read(64 * 1024);
//                    var_dump($msg);
//                });
            } else {
//                if (false === \swoole_process::kill($process->getPid(), 0)) {
//                    ProcessManager::getInstance()->removeProcessByKey($processName);
//                }
                // TODO 信号监听
                while ($ret = \swoole_process::wait(false)) {
                    $pid    = $ret['pid'];
                    $code   = $ret['code'];
                    $signal = $ret['signal'];
                    ProcessManager::getInstance()->removeProcessByPid($pid);
                }
                ProcessManager::getInstance()->writeByProcessName($processName, '--------->');
                $msg = ProcessManager::getInstance()->readByProcessName($processName);
                var_dump($msg);
            }
        };
        if ($this->_workerId == 1) {
            $this->_server->tick(5000, function () use ($runTask) {
//                $tasks = [
//                    array(
//                        'exec_count'      => 0,
//                        'single'      => 1,
//                        'run_status'      => '',
//                        'run_time_start'  => '',
//                        'run_time_update' => '',
//                        'task_name'       => 'test1',
//                        'cron_spec'       => '* * * * *',
//                        'group_id'        => '7',
//                        'timeout'         => '1',
//                        'status'          => '1',
//                        'command'         => '/usr/local/php/bin/php /home/www/test/index.php',
//                    ), array(
//                        'exec_count'      => 0,
//                        'single'      => 1,
//                        'run_status'      => '',
//                        'run_time_start'  => '',
//                        'run_time_update' => '',
//                        'task_name'       => 'test2',
//                        'cron_spec'       => '* * * * *',
//                        'group_id'        => '0',
//                        'timeout'         => '1',
//                        'status'          => '1',
//                        'command'         => '/usr/local/php/bin/php /home/www/test/index.php',
//                    )
//                ];
                $tasks = CronTasks::getInstance()->getTasks();
                if (!empty($tasks)) {
                    foreach ($tasks as $task) {
                        $task = CronLoadTasks::getInstance()->getTasks()->get($task['id']);
                        $runTask($task);
                    }
                }

//                TaskManager::getInstance()->add(function (){
//
//                });

            });
        }
    }
}