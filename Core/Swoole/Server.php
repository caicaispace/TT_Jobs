<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/1/22
 * Time: 下午9:55
 */

namespace Core\Swoole;


use Core\Conf\Event;
use Core\Swoole\Task\AAsyncTask;
use Core\AbstractInterface\IHttpExceptionHandler;
use Core\Component\Di;
use Core\Component\Error\Trigger;
use Core\Component\SuperClosure;
use Core\Component\SysConst;
use Core\Http\Dispatcher;
use Core\Http\Request;
use Core\Http\Response;
use Core\Swoole\Pipe\Dispatcher as PipeDispatcher;

class Server
{
    const SERVER_NOT_START = 0;
    const SERVER_STARTED   = 1;

    protected static $instance;
    protected        $swooleServer;
    protected        $isStart = self::SERVER_NOT_START;

    /*
     * 仅仅用于获取一个服务实例
     * @return Server
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    function __construct()
    {
        $conf = Config::getInstance();
        switch ($conf->getServerType()) {
            case Config::SERVER_TYPE_SERVER:
                $this->swooleServer = new \swoole_server(
                    $conf->getListenIp(),
                    $conf->getListenPort(),
                    $conf->getRunMode(),
                    $conf->getSocketType()
                );
                break;
            case Config::SERVER_TYPE_WEB:
                $this->swooleServer = new \swoole_http_server(
                    $conf->getListenIp(),
                    $conf->getListenPort(),
                    $conf->getRunMode()
                );
                break;
            case Config::SERVER_TYPE_WEB_SOCKET:
                $this->swooleServer = new \swoole_websocket_server(
                    $conf->getListenIp(),
                    $conf->getListenPort(),
                    $conf->getRunMode()
                );
                break;
            default:
                die('server type error');
                break;
        }
    }

    function isStart()
    {
        return $this->isStart;
    }

    /*
     * 创建并启动一个swoole http server
     */
    function startServer()
    {
        $conf = Config::getInstance();
        $this->getServer()->set($conf->getWorkerSetting());
        $this->beforeWorkerStart();
        $this->pipeMessage();
        $this->serverStartEvent();
        $this->serverShutdownEvent();
        $this->workerErrorEvent();
        $this->onTaskEvent();
        $this->onFinish();
        $this->workerStartEvent();
        $this->workerStopEvent();
        if ($conf->getSocketType() == Config::SERVER_TYPE_WEB_SOCKET) {
            $this->onMessage();
        } elseif ($conf->getServerType() != Config::SERVER_TYPE_SERVER) {
            $this->listenRequest();
        }
        $this->isStart = self::SERVER_STARTED;
        $this->getServer()->start();
    }

    /**
     * 用于获取 swoole_server 实例
     * server启动后，在每个进程中获得的，均为当前自身worker的server（可以理解为进程克隆后独立运行）
     *
     * @return \swoole_server
     */
    function getServer()
    {
        return $this->swooleServer;
    }

    /*
     * 监听http请求
     */
    private function listenRequest()
    {
        $this->getServer()->on("request", function (\swoole_http_request $request, \swoole_http_response $response) {
            $request2  = Request::getInstance($request);
            $response2 = Response::getInstance($response);
            try {
                Event::getInstance()->onRequest($request2, $response2);
                Dispatcher::getInstance()->dispatch();
                Event::getInstance()->onResponse($request2, $response2);
            } catch (\Exception $exception) {
                $handler = Di::getInstance()->get(SysConst::HTTP_EXCEPTION_HANDLER);
                if ($handler instanceof IHttpExceptionHandler) {
                    $handler->handler($exception, $request2, $response2);
                } else {
                    Trigger::exception($exception);
                }
            }
            $response2->end(true);
        });
    }

    private function workerStartEvent()
    {
        $this->getServer()->on("workerStart", function (\swoole_server $server, $workerId) {
            Event::getInstance()->onWorkerStart($server, $workerId);
        });
    }

    private function workerStopEvent()
    {
        $this->getServer()->on("workerStop", function (\swoole_server $server, $workerId) {
            Event::getInstance()->onWorkerStop($server, $workerId);
        });
    }

    private function onTaskEvent()
    {
        $num = Config::getInstance()->getTaskWorkerNum();
        if (!empty($num)) {
            $this->getServer()->on("task", function (\swoole_http_server $server, $taskId, $workerId, $taskObj) {
                try {
                    if (is_string($taskObj) && class_exists($taskObj)) {
                        $taskObj = new $taskObj();
                    }
                    Event::getInstance()->onTask($server, $taskId, $workerId, $taskObj);
                    if ($taskObj instanceof AAsyncTask) {
                        return $taskObj->handler($server, $taskId, $workerId);
                    } elseif ($taskObj instanceof SuperClosure) {
                        return $taskObj($server, $taskId);
                    }
                    return null;
                } catch (\Exception $exception) {
                    return null;
                }
            });
        }
    }

    private function onFinish()
    {
        $num = Config::getInstance()->getTaskWorkerNum();
        if (!empty($num)) {
            $this->getServer()->on("finish", function (\swoole_server $server, $taskId, $taskObj) {
                try {
                    Event::getInstance()->onFinish($server, $taskId, $taskObj);
                    //仅仅接受AbstractTask回调处理
                    if ($taskObj instanceof AAsyncTask) {
                        $taskObj->finishCallBack($server, $taskId, $taskObj->getDataForFinishCallBack());
                    }
                } catch (\Exception $exception) {

                }
            }
            );
        }
    }

    private function beforeWorkerStart()
    {
        Event::getInstance()->beforeWorkerStart($this->getServer());
    }

    private function serverStartEvent()
    {
        $this->getServer()->on("start", function (\swoole_server $server) {
            Event::getInstance()->onStart($server);
        });
    }

    private function serverShutdownEvent()
    {
        $this->getServer()->on("shutdown", function (\swoole_server $server) {
            Event::getInstance()->onShutdown($server);
        });
    }

    private function onMessage()
    {
        $this->getServer()->on("message", function (\swoole_server $server, $frame) {
            Event::getInstance()->onMessage($server, $frame);
        });
    }

    /*
     * 当worker/task_worker进程发生异常后会在Manager进程内回调此函数。
        $worker_id是异常进程的编号
        $worker_pid是异常进程的ID
        $exit_code退出的状态码，范围是 1 ～255
        此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。
        通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
     */
    private function workerErrorEvent()
    {
        $this->getServer()->on("workererror", function (\swoole_server $server, $worker_id, $worker_pid, $exit_code) {
            Event::getInstance()->onWorkerError($server, $worker_id, $worker_pid, $exit_code);
        });
    }

    private function pipeMessage()
    {
        $this->getServer()->on('pipeMessage', function (\swoole_server $server, $fromId, $data) {
            PipeDispatcher::getInstance()->dispatch($server, $fromId, $data);
        });
    }
}