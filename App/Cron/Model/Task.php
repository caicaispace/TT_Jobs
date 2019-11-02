<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/5
 * Time: 22:19
 */

namespace Cron\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class Task
 *
 * @package Cron\Model
 */
class Task extends Model
{
    protected $autoWriteTimestamp = true;
}