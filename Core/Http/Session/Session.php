<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Session;

use Core\Component\Di;
use Core\Component\SysConst;
use Core\Http\Request as HttpRequest;
use Core\Http\Response as HttpResponse;
use Core\Swoole\Task\TaskManager;
use Core\Utility\Random;

class Session
{
    private $sessionName;
    private $sessionSavePath;
    private $isStart = false;
    private $sessionHandler;
    private $sessionId;
    private static $staticInstance;

    public function __construct()
    {
        $handler = Di::getInstance()->get(SysConst::SESSION_HANDLER);
        if ($handler instanceof \SessionHandlerInterface) {
            $this->sessionHandler = $handler;
        } else {
            $this->sessionHandler = new SessionHandler();
        }
        $this->init();
    }

    public static function getInstance()
    {
        if (! isset(self::$staticInstance)) {
            self::$staticInstance = new Session();
        }
        return self::$staticInstance;
    }

    public function sessionName($name = null)
    {
        if ($name == null) {
            return $this->sessionName;
        }
        if ($this->isStart) {
            trigger_error("your can not change session name as {$name} when session is start");
            return false;
        }
        $this->sessionName = $name;
        return true;
    }

    public function sessionId($sid = null)
    {
        if ($sid === null) {
            return $this->sessionId;
        }
        if ($this->isStart) {
            trigger_error("your can not change session sid as {$sid} when session is start");
            return false;
        }
        $this->sessionId = $sid;
        return true;
    }

    public function savePath($path = null)
    {
        if ($path == null) {
            return $this->sessionSavePath;
        }
        if ($this->isStart) {
            trigger_error("your can not change session path as {$path} when session is start");
            return false;
        }
        $this->sessionSavePath = $path;
        return true;
    }

    public function isStart()
    {
        return $this->isStart;
    }

    public function start()
    {
        if (! $this->isStart) {
            $boolean = $this->sessionHandler->open($this->sessionSavePath, $this->sessionName);
            if (! $boolean) {
                trigger_error("session fail to open {$this->sessionSavePath} @ {$this->sessionName}");
                return false;
            }
            $probability = intval(Di::getInstance()->get(SysConst::SESSION_GC_PROBABILITY));
            $probability = $probability >= 30 ? $probability : 1000;
            if (mt_rand(0, $probability) == 1) {
                $handler = clone $this->sessionHandler;
                TaskManager::getInstance()->add(function () use ($handler) {
                    $set = Di::getInstance()->get(SysConst::SESSION_GC_MAX_LIFE_TIME);
                    if (! empty($set)) {
                        $maxLifeTime = $set;
                    } else {
                        $maxLifeTime = 3600 * 24 * 30;
                    }
                    $handler->gc($maxLifeTime);
                });
            }
            $request = HttpRequest::getInstance();
            $cookie  = $request->getCookieParams($this->sessionName);
            if ($this->sessionId) {
                //预防提前指定sid
                if ($this->sessionId != $cookie) {
                    $data = [
                        $this->sessionName => $this->sessionId,
                    ];
                    $request->withCookieParams($request->getRequestParam() + $data);
                    HttpResponse::getInstance()->setCookie($this->sessionName, $this->sessionId);
                }
            } else {
                if ($cookie === null) {
                    $sid  = $this->generateSid();
                    $data = [
                        $this->sessionName => $sid,
                    ];
                    $request->withCookieParams($request->getRequestParam() + $data);
                    HttpResponse::getInstance()->setCookie($this->sessionName, $sid);
                    $this->sessionId = $sid;
                } else {
                    $this->sessionId = $cookie;
                }
            }
            $this->isStart = 1;
            return true;
        }
        trigger_error('session has start');
        return false;
    }

    public function close()
    {
        if ($this->isStart) {
            $this->init();
            return $this->sessionHandler->close();
        }
        return true;
    }

    /*
     * 当执行read的时候，要求上锁
    */
    public function read()
    {
        return $this->sessionHandler->read($this->sessionId);
    }

    public function write($string)
    {
        return $this->sessionHandler->write($this->sessionId, $string);
    }

    public function destroy()
    {
        if ($this->sessionHandler->destroy($this->sessionId)) {
            return $this->close();
        }
        return false;
    }

    private function init()
    {
        $name                  = Di::getInstance()->get(SysConst::SESSION_NAME);
        $this->sessionName     = $name ? $name : 'Swoole';
        $this->sessionSavePath = Di::getInstance()->get(SysConst::SESSION_SAVE_PATH);
        $this->sessionId       = null;
        $this->isStart         = false;
    }

    private function generateSid()
    {
        return md5(microtime() . Random::randStr(3));
    }
}
