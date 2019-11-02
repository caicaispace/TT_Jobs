<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/18
 * Time: 下午12:43
 */

namespace Core\Swoole\Memory;


class TableManager
{
    private $list = [];

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
     * @param $name
     * @param array $columns ['col'=>['type'=>Table::TYPE_STRING,'size'=>1]]
     * @param int $size
     */
    public function add($name, array $columns, $size = 1024)
    {
        if (!isset($this->list[$name])) {
            $table = new \swoole_table($size);
            foreach ($columns as $column => $item) {
                $table->column($column, $item['type'], $item['size']);
            }
            $table->create();
            $this->list[$name] = $table;
        }
    }

    /**
     * 获取
     * @param $name
     * @return \swoole_table|null
     */
    public function get($name)
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        } else {
            return null;
        }
    }
}