<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/7/17
 * Time: 0:05:45
 */

namespace Common\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class AuthAccessLog
 *
 * @package Common\Model
 */
class AuthAccessLog extends Model
{
    protected $autoWriteTimestamp = true;
}