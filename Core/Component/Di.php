<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

class Di
{
    /*
     * 借以实现IOC注入
     */
    protected static $instance;
    protected $container = [];

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function set($key, $obj, ...$arg)
    {
        if (count($arg) == 1 && is_array($arg[0])) {
            $arg = $arg[0];
        }
        /*
         * 注入的时候不做任何的类型检测与转换
         * 由于编程人员为问题，该注入资源并不一定会被用到
         */
        $this->container[$key] = [
            'obj'    => $obj,
            'params' => $arg,
        ];
        return $this;
    }

    public function delete($key)
    {
        unset($this->container[$key]);
    }

    public function clear()
    {
        $this->container = [];
    }

    /**
     * @param $key
     * @return null|string
     */
    public function get($key)
    {
        if (isset($this->container[$key])) {
            $result = $this->container[$key];
            if (is_object($result['obj'])) {
                return $result['obj'];
            }
            if (is_callable($result['obj'])) {
                $ret                          = call_user_func_array($result['obj'], $result['params']);
                $this->container[$key]['obj'] = $ret;
                return $this->container[$key]['obj'];
            }
            if (is_string($result['obj']) && class_exists($result['obj'])) {
                $reflection                   = new \ReflectionClass($result['obj']);
                $ins                          = $reflection->newInstanceArgs($result['params']);
                $this->container[$key]['obj'] = $ins;
                return $this->container[$key]['obj'];
            }
            return $result['obj'];
        }
        return null;
    }
}
