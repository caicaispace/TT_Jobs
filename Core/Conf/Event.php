<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Conf;

use Core\AbstractInterface\AEvent;
use Core\AutoLoader;
use Core\Http\Request;
use Core\Http\Response;

/**
 * Class Event.
 */
class Event extends AEvent
{
    /**
     * @var AEvent
     */
    private $extendedEvent;

    public function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $className           = 'App\\' . APP_NAME . '\\Conf\\SwooleEvent';
        $this->extendedEvent = new $className();
    }

    public function frameInitialize()
    {
        // composer loader
        AutoLoader::getInstance()->requireFile('vendor/autoload.php');
        $this->extendedEvent->frameInitialize();
    }

    public function frameInitialized()
    {
        // think Db
        \think\Db::setConfig(Config::getInstance()->getConf('DATABASE.mysql.options'));
        $this->extendedEvent->frameInitialized();
    }

    public function beforeWorkerStart(\Swoole\Server $server)
    {
        $this->extendedEvent->beforeWorkerStart($server);
    }

    public function onStart(\Swoole\Server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    public function onShutdown(\Swoole\Server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    public function onWorkerStart(\Swoole\Server $server, $workerId)
    {
        // WebSocketCommandParser
//        \Core\Conf\WebSocketCommandParser::getInstance()->onWorkerStart($server, $workerId);
//
        $this->_AutoReload($server, $workerId);
        $this->extendedEvent->onWorkerStart($server, $workerId);
    }

    public function onWorkerStop(\Swoole\Server $server, $workerId)
    {
        $this->extendedEvent->onWorkerStop($server, $workerId);
    }

    public function onRequest(Request $request, Response $response)
    {
        $this->extendedEvent->onRequest($request, $response);
    }

    public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        $this->extendedEvent->onDispatcher($request, $response, $targetControllerClass, $targetAction);
    }

    public function onResponse(Request $request, Response $response)
    {
        $this->extendedEvent->onResponse($request, $response);
    }

    public function onTask(\Swoole\Server $server, $taskId, $workerId, $taskObj)
    {
        $this->extendedEvent->onTask($server, $taskId, $workerId, $taskObj);
    }

    public function onFinish(\Swoole\Server $server, $taskId, $taskObj)
    {
        $this->extendedEvent->onFinish($server, $taskId, $taskObj);
    }

    public function onWorkerError(\Swoole\Server $server, $workerId, $workerPid, $exitCode)
    {
        $this->extendedEvent->onWorkerError($server, $workerId, $workerPid, $exitCode);
    }

    public function onMessage(\Swoole\Server $server, $frame)
    {
        $this->extendedEvent->onMessage($server, $frame);
    }

    private function _AutoReload(\Swoole\Server $server, $workerId)
    {
        if ($workerId == 0) {
            if (extension_loaded('inotify')) {
//                $pid        = file_get_contents(Config::getInstance()->getConf("SERVER.CONFIG.pid_file"));
//                $autoReload = new \Core\Swoole\AutoReload($pid);
                $autoReload = new \Core\Swoole\AutoReload();
                $autoReload->watch(ROOT . '/App');
                $autoReload->run();
            }
        }
    }
}
