<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/24
 * Time: 11:25
 */

namespace Common;

use Core\Swoole\Process\AProcess;

/**
 * Class Process
 *
 * @package Common
 */
class Process extends AProcess
{
    public function run(\swoole_process $process)
    {
//        $this->addTick(1000, function () {
//            var_dump('this is ' . $this->getProcessName() . ' process tick');
//        });
        $this->delay(3000, function () use ($process) {
//            \exec('/usr/local/php/bin/php /home/www/test/index.php', $output, $status);
//            var_dump($output, $status);
//            $ret = $process->exec('/usr/local/php/bin/php', ['/home/www/test/index.php']);
//            var_dump($ret);
            var_dump('process delay 3 sec later run');
        });
    }

    public function onShutDown()
    {
        var_dump('process shut down');
    }

    /**
     * @param string $str
     * @param array  ...$args
     */
    public function onReceive($str, ...$args)
    {
        var_dump('process rec: ' . $str);
    }
}