<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\RPC\Server;

use Core\Component\RPC\AbstractInterface\AActionRegister;
use Core\Component\RPC\AbstractInterface\APackageParser;
use Core\Component\RPC\Common\ActionList;
use Core\Component\RPC\Common\Config;
use Core\Component\RPC\Common\Package;
use Core\Component\Socket\Client\TcpClient;
use Core\Component\Socket\Response;
use Core\Swoole\Server as SwooleServer;

class Server
{
    protected $config;
    private $serverList       = [];
    private $serverActionList = [];
    private $serverParser     = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
        if (empty($this->config->getPackageParserClass())) {
            exit('server conf need package parser class');
        }
    }

    public function registerServer($name)
    {
        if (isset($this->serverList[$name])) {
            return $this->serverList[$name];
        }
        $handler                 = new Service();
        $this->serverList[$name] = $handler;
        return $handler;
    }

    public function attach($port, $listenAddress = '0.0.0.0')
    {
        $listener = SwooleServer::getInstance()->getServer()->addlistener($listenAddress, $port, SWOOLE_TCP);
        $listener->set([
            'heartbeat_check_interval' => $this->config->getHeartBeatCheckInterval(),
            'open_eof_check'           => true,
            'package_eof'              => $this->config->getEof(),
        ]);
        $listener->on('receive', function (\swoole_server $server, $fd, $from_id, $data) {
            $data   = substr($data, 0, -strlen($this->config->getEof()));
            $client = new TcpClient($server->getClientInfo($fd));
            $client->setFd($fd);
            $client->setReactorId($from_id);
            $receivePackage = new Package();
            //借助成员变量实现伪单例模式，使得一个进程内仅有一个解析器对象。
            $parserClass = $this->config->getPackageParserClass();
            if (isset($this->serverParser[$parserClass])) {
                $this->serverParser[$parserClass];
            } else {
                if (class_exists($parserClass)) {
                    $this->serverParser[$parserClass] = new $parserClass();
                } else {
                    $this->serverParser[$parserClass] = false;
                }
            }
            $parser = $this->serverParser[$parserClass];
            if ($parser instanceof APackageParser) {
                $parser->decode($receivePackage, $client, $data);
                $serverName = $receivePackage->getServerName();
                $response   = new Package();
                $response->setServerName($serverName);
                $response->setAction($receivePackage->getAction());
                //判断有没有该服务
                if (isset($this->serverList[$serverName])) {
                    //存在该服务  还未进行action 注册的时候
                    if (! isset($this->serverActionList[$serverName])) {
                        $actionList = new ActionList();
                        $service    = $this->serverList[$serverName];
                        //获取行为注册类
                        $registerClass = $service->getActionRegisterClass();
                        if (class_exists($registerClass)) {
                            $ins = new $registerClass();
                            if ($ins instanceof AActionRegister) {
                                $ins->register($actionList);
                            }
                        }
                        $this->serverActionList[$serverName] = $actionList;
                    }
                    $actionList = $this->serverActionList[$serverName];
                    $action     = $receivePackage->getAction();
                    $call       = $actionList->getHandler($action);
                    if (is_callable($call)) {
                        try {
                            call_user_func_array($call, [$receivePackage, $response, $client]);
                        } catch (\Exception $exception) {
                            $response->setErrorCode($response::ERROR_SERVER_ERROR);
                            $response->setErrorMsg($exception->getTraceAsString());
                        }
                    } else {
                        $response->setErrorCode($response::ERROR_ACTION_NOT_FOUND);
                        $response->setErrorMsg("server @ {$serverName} action @ {$action} not found");
                    }
                } else {
                    $response->setErrorCode($response::ERROR_SERVER_NOT_FOUND);
                    $response->setErrorMsg("server @ {$serverName} not found");
                }
                $ret = $parser->encode($response);
                Response::response($client, (string) $ret, $this->config->getEof());
            } else {
                trigger_error("{$parserClass} is not a instance of APackageParser");
            }
        });
    }
}
