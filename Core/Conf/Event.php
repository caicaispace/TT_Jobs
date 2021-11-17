<?php
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

    public function beforeWorkerStart(\swoole_server $server)
    {
        $this->extendedEvent->beforeWorkerStart($server);
    }

    public function onStart(\swoole_server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    public function onShutdown(\swoole_server $server)
    {
        $this->extendedEvent->onStart($server);
    }

    public function onWorkerStart(\swoole_server $server, $workerId)
    {
        // WebSocketCommandParser
//        \Core\Conf\WebSocketCommandParser::getInstance()->onWorkerStart($server, $workerId);
//
        $this->_AutoReload($server, $workerId);
        $this->extendedEvent->onWorkerStart($server, $workerId);
    }

    public function onWorkerStop(\swoole_server $server, $workerId)
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

    public function onTask(\swoole_server $server, $taskId, $workerId, $taskObj)
    {
        $this->extendedEvent->onTask($server, $taskId, $workerId, $taskObj);
    }

    public function onFinish(\swoole_server $server, $taskId, $taskObj)
    {
        $this->extendedEvent->onFinish($server, $taskId, $taskObj);
    }

    public function onWorkerError(\swoole_server $server, $workerId, $workerPid, $exitCode)
    {
        $this->extendedEvent->onWorkerError($server, $workerId, $workerPid, $exitCode);
    }

    public function onMessage(\swoole_server $server, $frame)
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
                $autoReload->watch(ROOT . '/App');
                $autoReload->run();
            }
        }
    }
}
