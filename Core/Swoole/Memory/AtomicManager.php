<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/2/11
 * Time: 下午9:19
 */

namespace Core\Swoole\Memory;


class AtomicManager
{
    private $list = [];
    private $listForLong = [];

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
     * @param     $name
     * @param int $int
     */
    function add($name, $int = 0)
    {
        if (!isset($this->list[$name])) {
            $this->list[$name] = new \swoole_atomic($int); // 默认使用32位无符号类型
        }
    }

    /**
     * 获取
     * @param $name
     * @return \swoole_atomic|null
     */
    function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        } else {
            return NULL;
        }
    }

    /**
     * 添加 64
     * @param     $name
     * @param int $int
     */
    function addLong($name, $int = 0)
    {
        if (!isset($this->listForLong[$name])) {
            $this->listForLong[$name] = new \Swoole\Atomic\Long($int); // 如需要64有符号整型，可使用Swoole\Atomic\Long;
        }
    }

    /**
     * 获取 64
     * @param $name
     * @return \swoole_atomic|null
     */
    function getLong($name)
    {
        if (!isset($this->listForLong[$name])) {
            return $this->listForLong[$name];
        } else {
            return NULL;
        }
    }
}