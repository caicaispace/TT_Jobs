<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/27
 * Time: 0:47:44
 */

namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * 权限规则
 *
 * Class AuthRule
 *
 * @package Jobs\Model
 */
class AuthRule extends Model
{
    protected $autoWriteTimestamp = true;

    /* 认证方式，1 时时认证 2 登录认证 */
    const TYPE_REAL_TIME  = 1;
    const TYPE_LOGIN_TIME = 2;

    /* 状态：1 启用 0 禁用 */
    const STATUS_ENABLE  = 1;
    const STATUS_DISABLE = 0;

    const DELETED     = 1;
    const NOT_DELETED = 0;

    /* 常量反射 reflections */
    const RE = [
        'type'   => [
            self::TYPE_REAL_TIME  => '时时认证',
            self::TYPE_LOGIN_TIME => '登录认证',
        ],
        'status' => [
            self::STATUS_ENABLE  => '启用',
            self::STATUS_DISABLE => '禁用',
        ],
    ];
}