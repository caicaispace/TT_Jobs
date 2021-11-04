<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Http\Session;

class Base
{
    protected $session;

    public function __construct()
    {
        $this->session = Session::getInstance();
    }

    public function sessionName($name = null)
    {
        return $this->session->sessionName($name);
    }

    public function savePath($path = null)
    {
        return $this->session->savePath($path);
    }

    public function sessionId($sid = null)
    {
        return $this->session->sessionId($sid);
    }

    public function destroy()
    {
        return $this->session->destroy();
    }

    public function close()
    {
        return $this->session->close();
    }

    public function start()
    {
        if (! $this->session->isStart()) {
            return $this->session->start();
        }
        trigger_error('session has start');
        return false;
    }
}
