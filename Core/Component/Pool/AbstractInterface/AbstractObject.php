<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Pool\AbstractInterface;

abstract class AbstractObject
{
    public function __destruct()
    {
        $this->gc();
    }

    //使用后,free的时候会执行
    abstract public function initialize();
    abstract protected function gc();
}
