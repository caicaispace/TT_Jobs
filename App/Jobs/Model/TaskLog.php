<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class TaskLog.
 */
class TaskLog extends Model
{
    protected $autoWriteTimestamp = true;

    public static function init()
    {
    }
}
