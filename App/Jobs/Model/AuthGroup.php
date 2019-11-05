<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/27
 * Time: 0:47:30
 */

namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * 权限分组
 *
 * Class AuthGroup
 *
 * @package Jobs\Model
 */
class AuthGroup extends Model
{
    const DELETED     = 1;
    const NOT_DELETED = 0;
}