<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Conf;

use App\Jobs\Dispatcher\Dispatcher as JobsDispatcher;
use App\Jobs\Dispatcher\ProcessManager as JobsProcessManager;
use App\Jobs\Dispatcher\Tasks as JobsTasks;
use App\Jobs\Dispatcher\TasksLoad as JobsLoadTasks;
use App\Jobs\Event\onHttpDispatcher;
use Core\AbstractInterface\AEvent;
use Core\Http\Request;
use Core\Http\Response;
use think\db\Query;

/**
 * Class SwooleEvent.
 */
class SwooleEvent extends AEvent
{
    public function frameInitialize()
    {
        if (
            version_compare(phpversion('swoole'), '4.2.9', '>')
            and ! extension_loaded('swoole_async')
        ) {
            echo "---------------------------------------------------------------\n";
            echo "请安装 swoole async 模块 或降级 swoole 至 4.2.9 及以下版本\n";
            echo "---------------------------------------------------------------\n";
            exit();
        }
    }

    public function frameInitialized()
    {
//        (new Query)->listen(function ($sql, $time, $explain, $master) {
//            echo '[' . date('Y-m-d H:i:s') . '] ' . $sql . ' [' . $time . 's] ' . ($master ? 'master' : 'slave') . PHP_EOL;
////            var_dump($explain);
//        });
    }

    public function beforeWorkerStart(\swoole_server $server)
    {
        JobsTasks::getInstance();
        JobsLoadTasks::getInstance();
        JobsProcessManager::getInstance();
    }

    public function onStart(\swoole_server $server)
    {
    }

    public function onShutdown(\swoole_server $server)
    {
    }

    public function onWorkerStart(\swoole_server $server, $workerId)
    {
        JobsDispatcher::getInstance()->setServer($server, $workerId)->dispatch();
    }

    public function onWorkerStop(\swoole_server $server, $workerId)
    {
    }

    public function onRequest(Request $request, Response $response)
    {
    }

    public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        onHttpDispatcher::auth($request, $response, $targetControllerClass, $targetAction);
        onHttpDispatcher::accessLog($request, $response, $targetControllerClass, $targetAction);
    }

    public function onResponse(Request $request, Response $response)
    {
    }

    public function onTask(\swoole_server $server, $taskId, $workerId, $taskObj)
    {
    }

    public function onFinish(\swoole_server $server, $taskId, $taskObj)
    {
    }

    public function onWorkerError(\swoole_server $server, $workerId, $workerPid, $exitCode)
    {
    }

    public function onMessage(\swoole_server $server, $frame)
    {
    }
}
