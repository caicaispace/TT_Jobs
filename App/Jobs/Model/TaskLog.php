<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/23
 * Time: 0:56:25
 */

namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class TaskLog
 *
 * @package Jobs\Model
 */
class TaskLog extends Model
{
    protected $autoWriteTimestamp = true;

    public static function init()
    {

    }
}