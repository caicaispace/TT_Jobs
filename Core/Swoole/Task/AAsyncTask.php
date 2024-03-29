<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Task;

use Core\Swoole\Server;

abstract class AAsyncTask
{
    private $dataForFinishCallBack;
    private $dataForTask;

    public function __construct($dataForTask = null)
    {
        $this->dataForTask = $dataForTask;
    }

    /**
     * @return mixed
     */
    public function getDataForTask()
    {
        return $this->dataForTask;
    }

    /**
     * @param mixed $dataForTask
     */
    public function setDataForTask($dataForTask)
    {
        $this->dataForTask = $dataForTask;
    }

    /**
     * @return mixed
     */
    public function getDataForFinishCallBack()
    {
        return $this->dataForFinishCallBack;
    }

    /*
     * 注意   server为task进程的server   但taskId为分配该任务的主worker分配的taskId 为每个主worker进程内独立自增
     */
    abstract public function handler(\swoole_server $server, $taskId, $fromId);

    /*
     * 注意   server为主worker进程的server   但taskId为分配该任务的主worker分配的taskId 为每个主worker进程内独立自增
     */
    abstract public function finishCallBack(\swoole_server $server, $task_id, $resultData);

    protected function finish($dataForFinishCallBack = null)
    {
        if ($dataForFinishCallBack !== null) {
            $this->dataForFinishCallBack = $dataForFinishCallBack;
        }
        //为何不用$this传递   避免handler中有释放资源类型被序列化出错
        Server::getInstance()->getServer()->finish($this);
    }
}
