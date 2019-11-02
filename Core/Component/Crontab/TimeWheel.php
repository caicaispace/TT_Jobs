<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/20
 * Time: 0:30
 */

namespace Common\Cron;

use Core\Swoole\Memory\TableManager;
use Core\Utility\SnowFlake;

/**
 * Class TimeWheel
 * @package Common\Cron
 */
class TimeWheel
{
    const SWOOLE_TABLE_NAME = 'CRON_TIME_WHEEL';
    const TASKS_SIZE = 1024;

    private $_tableColumns = [
        'id' => ['type' => \swoole_table::TYPE_STRING, 'size' => 11]
    ];

    protected static $instance;

    static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function __construct()
    {
        for ($i = 1; $i <= 60; $i++) {
            $tableName = self::SWOOLE_TABLE_NAME . $i;
            TableManager::getInstance()->add($tableName, $this->_tableColumns, self::TASKS_SIZE);
        }
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
     * @return integer
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
        for ($i = 1; $i <= 60; $i++) {
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