<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

use think\Model;

abstract class AModel extends Model
{
    protected $autoWriteTimestamp = false;
    // 定义时间戳字段名
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';
}
