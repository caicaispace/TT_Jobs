<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/11
 * Time: 22:40
 */

namespace Core\Component\Crontab;

/**
 * Class Parse
 * @package Core\Component\Crontab
 */
class Parse
{
    static public $error;

    /**
     *  解析crontab的定时格式
     * @param string $crontabString
     *
     *      0     1    2    3    4    5
     *      *     *    *    *    *    *
     *      -     -    -    -    -    -
     *      |     |    |    |    |    |
     *      |     |    |    |    |    +----- day of week (0 - 6) (Sunday=0)
     *      |     |    |    |    +----- month (1 - 12)
     *      |     |    |    +------- day of month (1 - 31)
     *      |     |    +--------- hour (0 - 23)
     *      |     +----------- min (0 - 59)
     *      +------------- sec (0-59)
     * @param int    $startTime timestamp [default=current timestamp]
     * @return int|array unix timestamp  下一分钟内是否需要执行任务，如果需要，则把需要在那几秒执行返回
     * @throws \InvalidArgumentException 错误信息
     */
    static public function parse($crontabString, $startTime = null)
    {
        if (is_array($crontabString)) {
            return self::_parseArray($crontabString, $startTime);
        }
        if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontabString))) {
            if (!preg_match('/^((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)\s+((\*(\/[0-9]+)?)|[0-9\-\,\/]+)$/i', trim($crontabString))) {
                self::$error = "Invalid cron string: " . $crontabString;
                return false;
            }
        }
        if ($startTime && !is_numeric($startTime)) {
            self::$error = "{$startTime} must be a valid unix timestamp ({$startTime} given)";
            return false;
        }
        $cron  = preg_split("/[\s]+/i", trim($crontabString));
        $start = empty($startTime) ? time() : $startTime;
        $date  = [];
        if (count($cron) == 6) {
            $date = [
                'second'  => self::_parseCronNumber($cron[0], 0, 59),
                'minutes' => self::_parseCronNumber($cron[1], 0, 59),
                'hours'   => self::_parseCronNumber($cron[2], 0, 23),
                'day'     => self::_parseCronNumber($cron[3], 1, 31),
                'month'   => self::_parseCronNumber($cron[4], 1, 12),
                'week'    => self::_parseCronNumber($cron[5], 0, 6),
            ];
        } elseif (count($cron) == 5) {
            $date = [
                'second'  => [1 => 1],
                'minutes' => self::_parseCronNumber($cron[0], 0, 59),
                'hours'   => self::_parseCronNumber($cron[1], 0, 23),
                'day'     => self::_parseCronNumber($cron[2], 1, 31),
                'month'   => self::_parseCronNumber($cron[3], 1, 12),
                'week'    => self::_parseCronNumber($cron[4], 0, 6),
            ];
        }
        if (
            in_array(intval(date('i', $start)), $date['minutes']) &&
            in_array(intval(date('G', $start)), $date['hours']) &&
            in_array(intval(date('j', $start)), $date['day']) &&
            in_array(intval(date('w', $start)), $date['week']) &&
            in_array(intval(date('n', $start)), $date['month'])
        ) {
            return $date['second'];
        }
        return null;
    }

    /**
     * 解析单个配置的含义
     * @param $s
     * @param $min
     * @param $max
     * @return array
     */
    static protected function _parseCronNumber($s, $min, $max)
    {
        $result = [];
        $v1     = explode(",", $s);
        foreach ($v1 as $v2) {
            $v3   = explode("/", $v2);
            $step = empty($v3[1]) ? 1 : $v3[1];
            $v4   = explode("-", $v3[0]);
            $_min = count($v4) == 2 ? $v4[0] : ($v3[0] == "*" ? $min : $v3[0]);
            $_max = count($v4) == 2 ? $v4[1] : ($v3[0] == "*" ? $max : $v3[0]);
            for ($i = $_min; $i <= $_max; $i += $step) {
                $result[$i] = intval($i);
            }
        }
        ksort($result);
        return $result;
    }

    /**
     * @param $data
     * @param $startTime
     * @return array
     */
    static protected function _parseArray($data, $startTime)
    {
        $result = [];
        foreach ($data as $v) {
            if (count(explode(":", $v)) == 2) {
                $v = $v . ":01";
            }
            $time = strtotime($v);
            if ($time >= $startTime && $time < $startTime + 60) {
                $result[$time] = $time;
            }
        }
        return $result;
    }
}