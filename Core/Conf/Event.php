<?php

namespace Core\Conf;

use Core\AbstractInterface\AEvent;
use Core\Http\Request;
use Core\Http\Response;
use Core\AutoLoader;

/**
 * Class Event
 *
 * @package Core\Conf
 */
class Event extends AEvent
{
    /**
     * @var AEvent
     */
    private $extendedEvent;

    function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        $className           = 'App\\' . APP_NAME . '\\Conf\\SwooleEvent';
        $this->extendedEvent = new $className;
    }

    function frameInitialize()
    {
        // composer loader
        AutoLoader::getInstance()->requireFile('vendor/autoload.php');
        $this->extendedEvent->frameInitialize();
    }

    function frameInitialized()
    {
        // think Db
        \think\Db::setConfig(Config::getInstance()->getConf('DATABASE.mysql.options'));
        $this->extendedEvent->frameInitialized();
    }

    function beforeWorkerStart(\swoole_server $server)
    {
        $this->extendedEvent->beforeWorkerStart($server);
    }

    function onStart(\swoole_server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    function onShutdown(\swoole_server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    function onWorkerStart(\swoole_server $server, $workerId)
    {
        // WebSocketCommandParser
//        \Core\Conf\WebSocketCommandParser::getInstance()->onWorkerStart($server, $workerId);
//
        $this->_AutoReload($server, $workerId);
        $this->extendedEvent->onWorkerStart($server, $workerId);
    }

    function onWorkerStop(\swoole_server $server, $workerId)
    {
        $this->extendedEvent->onWorkerStop($server, $workerId);
    }

    function onRequest(Request $request, Response $response)
    {
        $this->extendedEvent->onRequest($request, $response);
    }

    function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        $this->extendedEvent->onDispatcher($request, $response, $targetControllerClass, $targetAction);
    }

    function onResponse(Request $request, Response $response)
    {
        $this->extendedEvent->onResponse($request, $response);
    }

    function onTask(\swoole_server $server, $taskId, $workerId, $taskObj)
    {
        $this->extendedEvent->onTask($server, $taskId, $workerId, $taskObj);
    }

    function onFinish(\swoole_server $server, $taskId, $taskObj)
    {
        $this->extendedEvent->onFinish($server, $taskId, $taskObj);
    }

    function onWorkerError(\swoole_server $server, $workerId, $workerPid, $exitCode)
    {
        $this->extendedEvent->onWorkerError($server, $workerId, $workerPid, $exitCode);
    }

    function onMessage(\swoole_server $server, $frame)
    {
        $this->extendedEvent->onMessage($server, $frame);
    }

    private function _AutoReload(\swoole_server $server, $workerId)
    {
        if ($workerId == 0) {
            if (extension_loaded('inotify')) {
//                $pid        = file_get_contents(Config::getInstance()->getConf("SERVER.CONFIG.pid_file"));
//                $autoReload = new \Core\Swoole\AutoReload($pid);
                $autoReload = new \Core\Swoole\AutoReload();
                $autoReload->watch(ROOT . "/App");
                $autoReload->run();
            }
        }
    }
}
