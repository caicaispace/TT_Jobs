<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/7/17
 * Time: 0:05:45
 */

namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class AuthAccessLog
 *
 * @package Jobs\Model
 */
class AuthAccessLog extends Model
{
    protected $autoWriteTimestamp = true;
}