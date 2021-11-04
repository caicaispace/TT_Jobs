<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Pool\AbstractInterface;

use Core\Component\Pool\PoolManager;

/**
 * Class Pool.
 */
abstract class Pool
{
    protected $minNum;
    protected $maxNum;
    protected $poolTableKey;
    protected $poolTable;
    protected $queue;

    final public function __construct($min, $max, $poolTableKey)
    {
        $this->minNum       = $min;
        $this->maxNum       = $max;
        $this->poolTableKey = $poolTableKey;
        $this->queue        = new \SplQueue();
        $this->poolTable    = PoolManager::getInstance()->getPoolTable();
        for ($i = 0; $i < $this->minNum; ++$i) {
            $obj = $this->createObject();
            if ($obj) {
                $this->poolTable->incr($poolTableKey, 'createNum');
                $this->queue->enqueue($obj);
            }
        }
    }

    /**
     * @param float $timeOut
     * @return null|mixed
     */
    public function getObj($timeOut = 0.1)
    {
        //超时机制 后续实现
        $obj = null;
        if ($this->queue->isEmpty()) {
            //用inc方式实现进程内协程锁
            $testNum = $this->poolTable->incr($this->poolTableKey, 'createNum');
            if ($testNum !== false) {
                //若队列为空，则判断能否创建
                if ($testNum <= $this->maxNum) {
                    $obj = $this->createObject();
                } else {
                    $this->poolTable->decr($this->poolTableKey, 'createNum');
                }
            }
        } else {
            $obj = $this->queue->dequeue();
        }
        return $obj;
    }

    /**
     * @param $obj
     */
    public function freeObj($obj)
    {
        if ($obj instanceof AbstractObject) {
            $obj->initialize();
        }
        $this->queue->enqueue($obj);
    }

    abstract protected function createObject();
}
