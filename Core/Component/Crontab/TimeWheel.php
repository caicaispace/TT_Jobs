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
        'id' => ['type' => \Swoole\Table::TYPE_STRING, 'size' => 11],
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

    public function push(array $data): bool
    {
        $minute = date('i');
        $table  = $this->_getTable($minute);
        $key    = (string) SnowFlake::make();
        return $table->set($key, $data);
    }

    public function pop(): \Swoole\Table
    {
        return $this->table();
    }

    public function table(): \Swoole\Table
    {
        $minute = date('i');
        return $this->_getTable($minute);
    }

    public function count(): int
    {
        $minute = date('i');
        return $this->_getTable($minute)->count();
    }

    public function clear(): bool
    {
        for ($i = 1; $i <= 60; ++$i) {
            $table = $this->_getTable((string) $i);
            foreach ($table as $k => $v) {
                $table->del($k);
            }
        }
        return true;
    }

    public function del(string $key): bool
    {
        $minute = date('i');
        $table  = $this->_getTable($minute);
        return $table->del($key);
    }

    private function _getTable(string $minute): ?\Swoole\Table
    {
        $tableName = self::SWOOLE_TABLE_NAME . $minute;
        return TableManager::getInstance()->get($tableName);
    }
}
