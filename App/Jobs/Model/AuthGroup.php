<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * 权限分组.
 *
 * Class AuthGroup
 */
class AuthGroup extends Model
{
    public const DELETED     = 1;
    public const NOT_DELETED = 0;
}
