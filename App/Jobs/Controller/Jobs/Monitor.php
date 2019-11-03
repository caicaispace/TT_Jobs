<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/22
 * Time: 11:55
 */

namespace App\Jobs\Controller\Jobs;

use Core\AbstractInterface\ARESTController as Controller;


class Monitor extends Controller
{
    function GET_index()
    {

    }

    private function _getCpuInfo()
    {
        $cpu  = [];
        $str  = file_get_contents("/proc/stat");
        $mode = "/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
        preg_match_all($mode, $str, $cpu);
        $total = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[5][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        $time  = $cpu[2][0] + $cpu[3][0] + $cpu[4][0] + $cpu[6][0] + $cpu[7][0] + $cpu[8][0] + $cpu[9][0];
        return [
            'total' => $total,
            'time'  => $time,
        ];
    }
}