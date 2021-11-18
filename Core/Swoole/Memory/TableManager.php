<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Swoole\Memory;

class TableManager
{
    protected static $instance;
    private array $list = [];

    public static function getInstance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 添加.
     * @param array $columns ['col'=>['type'=>Table::TYPE_STRING,'size'=>1]]
     */
    public function add(string $name, array $columns, int $size = 1024)
    {
        if (! isset($this->list[$name])) {
            $table = new \Swoole\Table($size);
            foreach ($columns as $column => $item) {
                $table->column($column, $item['type'], $item['size']);
            }
            $table->create();
            $this->list[$name] = $table;
        }
    }

    /**
     * 获取.
     */
    public function get(string $name): ?\Swoole\Table
    {
        if (isset($this->list[$name])) {
            return $this->list[$name];
        }
        return null;
    }
}
