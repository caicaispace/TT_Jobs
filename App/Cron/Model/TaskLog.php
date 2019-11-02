<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/23
 * Time: 0:56:25
 */

namespace Cron\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class TaskLog
 *
 * @package Cron\Model
 */
class TaskLog extends Model
{
    protected $autoWriteTimestamp = true;

    public static function init()
    {

    }
}