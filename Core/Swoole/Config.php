<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole;

use Core\Conf\Config as GlobalConf;

class Config
{
    public const SERVER_TYPE_SERVER     = 'SERVER_TYPE_SERVER';
    public const SERVER_TYPE_WEB        = 'SERVER_TYPE_WEB';
    public const SERVER_TYPE_WEB_SOCKET = 'SERVER_TYPE_WEB_SOCKET';

    private $listenIp;
    private $listenPort;
    private $workerSetting;
    private $workerNum;
    private $taskWorkerNum;
    private $serverName;
    private $runMode;
    private $serverType;
    private $socketType;
    private static $instance;

    public function __construct()
    {
        $this->listenIp      = GlobalConf::getInstance()->getConf('SERVER.LISTEN');
        $this->listenPort    = GlobalConf::getInstance()->getConf('SERVER.PORT');
        $this->workerSetting = GlobalConf::getInstance()->getConf('SERVER.CONFIG');
        $this->workerNum     = GlobalConf::getInstance()->getConf('SERVER.CONFIG.worker_num');
        $this->taskWorkerNum = GlobalConf::getInstance()->getConf('SERVER.CONFIG.task_worker_num');
        $this->serverName    = GlobalConf::getInstance()->getConf('SERVER.SERVER_NAME');
        $this->runMode       = GlobalConf::getInstance()->getConf('SERVER.RUN_MODE');
        $this->serverType    = GlobalConf::getInstance()->getConf('SERVER.SERVER_TYPE');
        $this->socketType    = GlobalConf::getInstance()->getConf('SERVER.SOCKET_TYPE');
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function getListenIp()
    {
        return $this->listenIp;
    }

    public function getListenPort()
    {
        return $this->listenPort;
    }

    public function getWorkerSetting()
    {
        return $this->workerSetting;
    }

    public function getWorkerNum()
    {
        return $this->workerNum;
    }

    public function getTaskWorkerNum()
    {
        return $this->taskWorkerNum;
    }

    public function getServerName()
    {
        return $this->serverName;
    }

    public function getRunMode()
    {
        return $this->runMode;
    }

    public function getServerType()
    {
        return $this->serverType;
    }

    public function getSocketType()
    {
        return $this->socketType;
    }
}
