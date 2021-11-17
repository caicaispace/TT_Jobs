<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Memory;

class AtomicManager
{
    protected static $instance;
    private $list        = [];
    private $listForLong = [];

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
     * @param int $int
     */
    public function add($name, $int = 0)
    {
        if (! isset($this->list[$name])) {
            $this->list[$name] = new \swoole_atomic($int); // 默认使用32位无符号类型
        }
    }

    /**
     * 获取.
     * @param $name
     * @return null|\swoole_atomic
     */
    public function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }

    /**
     * 添加 64.
     * @param $name
     * @param int $int
     */
    public function addLong($name, $int = 0)
    {
        if (! isset($this->listForLong[$name])) {
            $this->listForLong[$name] = new \Swoole\Atomic\Long($int); // 如需要64有符号整型，可使用Swoole\Atomic\Long;
        }
    }

    /**
     * 获取 64.
     * @param $name
     * @return null|\swoole_atomic
     */
    public function getLong($name)
    {
        if (! isset($this->listForLong[$name])) {
            return $this->listForLong[$name];
        }
        return null;
    }
}
