<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Pool;

use Core\Component\Error\Trigger;
use Core\Component\Pool\AbstractInterface\Pool;
use Core\Conf\Config;
use Core\Swoole\Memory\TableManager;
use Swoole\Table;

/**
 * Class PoolManager.
 */
class PoolManager
{
    public const TYPE_ONLY_WORKER      = 1;
    public const TYPE_ONLY_TASK_WORKER = 2;
    public const TYPE_ALL_WORKER       = 3;

    protected static $instance;
    private $poolTable;
    private $poolClassList  = [];
    private $poolObjectList = [];

    public function __construct()
    {
        TableManager::getInstance()->add('__PoolManager', [
            'createNum' => ['type' => Table::TYPE_INT, 'size' => 3],
        ], 8192);
        $this->poolTable = TableManager::getInstance()->get('__PoolManager');

        $conf = Config::getInstance()->getConf('POOL_MANAGER');
        if (is_array($conf)) {
            foreach ($conf as $class => $item) {
                $this->registerPool($class, $item['min'], $item['max'], $item['type']);
            }
        }
    }

    /**
     * 为自定义进程预留.
     * @param $workerId
     */
    public function __workerStartHook($workerId)
    {
        $workerNum = Config::getInstance()->getConf('MAIN_SERVER.SETTING.worker_num');
        foreach ($this->poolClassList as $class => $item) {
            if ($item['type'] === self::TYPE_ONLY_WORKER) {
                if ($workerId > ($workerNum - 1)) {
                    continue;
                }
            } elseif ($item['type'] === self::TYPE_ONLY_TASK_WORKER) {
                if ($workerId <= ($workerNum - 1)) {
                    continue;
                }
            }
            $key = self::generateTableKey($class, $workerId);
            $this->poolTable->del($key);
            $this->poolObjectList[$class] = new $class($item['min'], $item['max'], $key);
        }
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param string $class
     * @param $minNum
     * @param $maxNum
     * @param int $type
     * @return bool
     */
    public function registerPool($class, $minNum, $maxNum, $type = self::TYPE_ONLY_WORKER)
    {
        try {
            $ref = new \ReflectionClass($class);
            if ($ref->isSubclassOf(Pool::class)) {
                $this->poolClassList[$class] = [
                    'min'  => $minNum,
                    'max'  => $maxNum,
                    'type' => $type,
                ];
                return true;
            }
            Trigger::error($class . ' is not Pool class');
        } catch (\Throwable $throwable) {
            Trigger::error($throwable);
        }
        return false;
    }

    /**
     * @param string $class
     * @return null|Pool
     */
    public function getPool($class)
    {
        if (isset($this->poolObjectList[$class])) {
            return $this->poolObjectList[$class];
        }
        return null;
    }

    /**
     * @return null|\swoole_table
     */
    public function getPoolTable()
    {
        return $this->poolTable;
    }

    /**
     * @param string $class
     * @param int $workerId
     * @return string
     */
    public static function generateTableKey($class, $workerId)
    {
        return substr(md5($class . $workerId), 8, 16);
    }
}
