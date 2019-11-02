<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/18
 * Time: 下午12:35
 */

namespace Core\Swoole\Memory;


class ChannelManager
{
    private $list = [];

    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 添加
     * @param $name
     * @param float|int $size
     */
    function add($name, $size = 1024 * 256)
    {
        if (!isset($this->list[$name])) {
            $chan = new \swoole_channel($size);
            $this->list[$name] = $chan;
        }
    }

    /**
     * 获取
     * @param $name
     * @return \swoole_channel|null
     */
    function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        } else {
            return null;
        }
    }
}