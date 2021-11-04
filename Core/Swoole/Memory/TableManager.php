<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Memory;

class TableManager
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
     * @param array $columns ['col'=>['type'=>Table::TYPE_STRING,'size'=>1]]
     * @param int $size
     */
    public function add($name, array $columns, $size = 1024)
    {
        if (! isset($this->list[$name])) {
            $table = new \swoole_table($size);
            foreach ($columns as $column => $item) {
                $table->column($column, $item['type'], $item['size']);
            }
            $table->create();
            $this->list[$name] = $table;
        }
    }

    /**
     * 获取.
     * @param $name
     * @return null|\swoole_table
     */
    public function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }
}
