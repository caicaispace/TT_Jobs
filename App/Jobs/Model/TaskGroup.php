<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/16
 * Time: 17:26
 */

namespace App\Jobs\Model;

use Core\AbstractInterface\AModel as Model;

/**
 * Class TaskGroup
 *
 * @package Home\Model
 */
class TaskGroup extends Model
{
    protected $autoWriteTimestamp = true;

    const DELETED   = 1;
    const UN_DELETE = 0;

    public static function init()
    {
//        self::event('before_insert', function ($table) {
//            $table->sex = \mt_rand(0, 1);
//        });
    }
}
