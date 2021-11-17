<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * 权限规则.
 *
 * Class AuthRule
 */
class AuthRule extends Model
{
    /* 认证方式，1 时时认证 2 登录认证 */
    public const TYPE_REAL_TIME  = 1;
    public const TYPE_LOGIN_TIME = 2;

    /* 状态：1 启用 0 禁用 */
    public const STATUS_ENABLE  = 1;
    public const STATUS_DISABLE = 0;

    public const DELETED     = 1;
    public const NOT_DELETED = 0;

    /* 常量反射 reflections */
    public const RE = [
        'type'   => [
            self::TYPE_REAL_TIME  => '时时认证',
            self::TYPE_LOGIN_TIME => '登录认证',
        ],
        'status' => [
            self::STATUS_ENABLE  => '启用',
            self::STATUS_DISABLE => '禁用',
        ],
    ];
    protected $autoWriteTimestamp = true;
}
