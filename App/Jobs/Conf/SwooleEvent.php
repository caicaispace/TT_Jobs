<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Conf;

use App\Jobs\Dispatcher\Dispatcher as JobsDispatcher;
use App\Jobs\Dispatcher\ProcessManager as JobsProcessManager;
use App\Jobs\Dispatcher\Tasks as JobsTasks;
use App\Jobs\Dispatcher\TasksLoad as JobsLoadTasks;
use App\Jobs\Event\OnHttpDispatcher;
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
        // if (
        //     version_compare(phpversion('swoole'), '4.2.9', '>')
        //     and ! extension_loaded('swoole_async')
        // ) {
        //     echo "---------------------------------------------------------------\n";
        //     echo "请安装 swoole async 模块 或降级 swoole 至 4.2.9 及以下版本\n";
        //     echo "---------------------------------------------------------------\n";
        //     exit();
        // }
    }

    public function frameInitialized()
    {
//        (new Query)->listen(function ($sql, $time, $explain, $master) {
//            echo '[' . date('Y-m-d H:i:s') . '] ' . $sql . ' [' . $time . 's] ' . ($master ? 'master' : 'slave') . PHP_EOL;
////            var_dump($explain);
//        });
    }

    public function beforeWorkerStart(\Swoole\Server $server)
    {
        JobsTasks::getInstance();
        JobsLoadTasks::getInstance();
        JobsProcessManager::getInstance();
    }

    public function onStart(\Swoole\Server $server)
    {
    }

    public function onShutdown(\Swoole\Server $server)
    {
    }

    public function onWorkerStart(\Swoole\Server $server, $workerId)
    {
        JobsDispatcher::getInstance()->setServer($server, $workerId)->dispatch();
    }

    public function onWorkerStop(\Swoole\Server $server, $workerId)
    {
    }

    public function onRequest(Request $request, Response $response)
    {
    }

    public function onDispatcher(Request $request, Response $response, $targetControllerClass, $targetAction)
    {
        OnHttpDispatcher::auth($request, $response, $targetControllerClass, $targetAction);
        OnHttpDispatcher::accessLog($request, $response, $targetControllerClass, $targetAction);
    }

    public function onResponse(Request $request, Response $response)
    {
    }

    public function onTask(\Swoole\Server $server, $taskId, $workerId, $taskObj)
    {
    }

    public function onFinish(\Swoole\Server $server, $taskId, $taskObj)
    {
    }

    public function onWorkerError(\Swoole\Server $server, $workerId, $workerPid, $exitCode)
    {
    }

    public function onMessage(\Swoole\Server $server, $frame)
    {
    }
}
