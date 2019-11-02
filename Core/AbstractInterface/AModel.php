<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/16
 * Time: 19:02
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