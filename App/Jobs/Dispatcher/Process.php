<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/13
 * Time: 23:06
 */

namespace App\Jobs\Dispatcher;

use Core\Swoole\Memory\TableManager;

/**
 * Class Process
 *
 * @package Jobs\Dispatcher
 */
class Process
{
    private $_key;
    private $_pid;
    private $_onFinish;
    private $_processName;
    private $_args = [];

    /**
     * Process constructor.
     *
     * @param string   $key
     * @param string   $processName
     * @param callable $onFinish
     * @param array    $args
     */
    function __construct($key, $processName, $onFinish, array $args)
    {
        $pid = \swoole_async::exec($processName, [$this, 'onFinish']);

        $this->_args        = $args;
        $this->_key         = $key;
        $this->_onFinish    = $onFinish;
        $this->_processName = $processName;
        $this->_pid         = $pid;

        TableManager::getInstance()->get(ProcessManager::SWOOLE_TABLE_NAME)->set(md5($key), ['pid' => $pid]);
    }

    /**
     * @return int|null
     */
    function getPid()
    {
        return $this->_pid;
    }

    function getProcessKey()
    {
        return $this->_key;
    }

    /**
     * @return string
     */
    function getProcessName()
    {
        return $this->_processName;
    }

    /**
     * @return array
     */
    function getArgs()
    {
        return $this->_args;
    }

    /**
     * @param $key
     *
     * @return mixed|null
     */
    function getArg($key)
    {
        return isset($this->_args[$key])
            ? $this->_args[$key]
            : null;
    }

    /**
     * @param $result
     * @param $status
     */
    function onFinish($result, $status)
    {
        call_user_func($this->_onFinish, $result, $status, $this->_args);
    }

    /**
     * @param string      $str
     * @param array|mixed ...$args
     */
    function onReceive($str, ...$args)
    {
    }
}