<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Common\Cron;

use Core\Swoole\Memory\TableManager;
use Core\Utility\SnowFlake;

/**
 * Class TimeWheel.
 */
class TimeWheel
{
    public const SWOOLE_TABLE_NAME = 'CRON_TIME_WHEEL';
    public const TASKS_SIZE        = 1024;

    protected static $instance;

    private $_tableColumns = [
        'id' => ['type' => \swoole_table::TYPE_STRING, 'size' => 11],
    ];

    public function __construct()
    {
        for ($i = 1; $i <= 60; ++$i) {
            $tableName = self::SWOOLE_TABLE_NAME . $i;
            TableManager::getInstance()->add($tableName, $this->_tableColumns, self::TASKS_SIZE);
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
     * @param $data
     * @return bool
     */
    public function push($data)
    {
        $minute = date('i');
        $table  = $this->_getTable($minute);
        $key    = SnowFlake::make();
        return $table->set($key, $data);
    }

    /**
     * @return \swoole_table
     */
    public function pop()
    {
        return $this->table();
    }

    /**
     * @return \swoole_table
     */
    public function table()
    {
        $minute = date('i');
        return $this->_getTable($minute);
    }

    /**
     * @return int
     */
    public function count()
    {
        $minute = date('i');
        return $this->_getTable($minute)->count();
    }

    /**
     * @return bool
     */
    public function clear()
    {
        for ($i = 1; $i <= 60; ++$i) {
            $table = $this->_getTable($i);
            foreach ($table as $k => $v) {
                $table->del($k);
            }
        }
        return true;
    }

    /**
     * @param $key
     * @return bool
     */
    public function del($key)
    {
        $minute = date('i');
        $table  = $this->_getTable($minute);
        return $table->del($key);
    }

    /**
     * @param $minute
     * @return null|\swoole_table
     */
    private function _getTable($minute)
    {
        $tableName = self::SWOOLE_TABLE_NAME . $minute;
        return TableManager::getInstance()->get($tableName);
    }
}
