<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Memory;

class ChannelManager
{
    protected static $instance;
    private $list = [];

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 添加.
     * @param $name
     * @param float|int $size
     */
    public function add($name, $size = 1024 * 256)
    {
        if (! isset($this->list[$name])) {
            $chan              = new \swoole_channel($size);
            $this->list[$name] = $chan;
        }
    }

    /**
     * 获取.
     * @param $name
     * @return null|\swoole_channel
     */
    public function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }
}
