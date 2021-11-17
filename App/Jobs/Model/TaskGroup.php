<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class TaskGroup.
 */
class TaskGroup extends Model
{
    public const DELETED          = 1;
    public const UN_DELETE        = 0;
    protected $autoWriteTimestamp = true;

    public static function init()
    {
//        self::event('before_insert', function ($table) {
//            $table->sex = \mt_rand(0, 1);
//        });
    }
}
