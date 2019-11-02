<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/11
 * Time: 23:06
 */

namespace Cron\Controller\Cron;


use Core\AbstractInterface\ARESTController as Controller;
use Core\Component\Crontab\Parse;
use Cron\CronExpression;
use Cron\Logic\Task as TaskLogic;
use Core\Utility\Auth\Web as Auth;


class Test extends Controller
{
    function GET_auth()
    {
        $auth = new Auth;
        try {
            $ret = $auth->check('test', 1);
            var_dump($ret);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    function GET_index()
    {
//        var_dump(date('YmdHi'));
//        return;
//        $taskLogic = new TaskLogic;
//        $ret = $taskLogic->getList();
//        $list = $ret->getData();
//        foreach ($list as $k => $v) {
//            var_dump(md5($v['task_name']));
//        }

//        $cronString = '*/5 * * * * *';
        $cronString = '0 */12 * * * *';
        $cronString = '20 * * * *';
        // $cronString = '0 * * * *';
//        $cronString = '0 0 * * *';
//        $cronString = '0 0 * * 0';
//        $cronString = '0 0 1 * *';
//        $cronString = '0 0 1 1 *';
//        $cronString = '0 0 12 * * ?';
//        $cronString = '0 15 10 * * ? *';

//         $cron      = CronExpression::factory($cronString);
//         $runMinute = (int) $cron->getNextRunDate('2018-06-24 01:59:00')->format('YmdHi');
//         $minute    = (int) date("YmdHi", strtotime('2018-06-24 01:59:00'));
//         // $runMinute = (int) $cron->getNextRunDate('2018-06-24 01:59:00')->getTimestamp();
// //        $minute    = (int) strtotime('2018-06-24 01:59:00');
//         var_dump($runMinute);
//         var_dump($minute);
//         var_dump($runMinute - $minute);return;
// //        if (($runMinute - $minute) > 1) {
// //            continue;
// //        }

        $responseData = [];
        if (CronExpression::isValidExpression($cronString)) {
            $ret = CronExpression::factory($cronString)->getMultipleRunDates(10, '2018-06-24 01:00:00');
            foreach ($ret as $k => $v) {
                $date = $v->format('YmdHi');
                var_dump($date);
                $responseData[] = $date;
            }
        }
        $this->response()->writeJson(200, $responseData);
    }
}