<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/13
 * Time: 23:06
 */

namespace Common\Cron;

use Core\Swoole\Process\AProcess;

/**
 * Class HandleProcess
 * @package Common\Cron
 */
class _HandleProcess extends AProcess
{

    public function initialize($processName, $args, $async)
    {
    }

    /**
     * @param \swoole_process $process
     */
    public function run(\swoole_process $process)
    {
        $command = $this->getArg('command');
        list($programPath, $file) = explode(' ', $command);
        $process->exec($programPath, [$file]);
    }

    public function onShutDown()
    {
    }

    /**
     * @param             $str
     * @param array|mixed ...$args
     */
    public function onReceive($str, ...$args)
    {
    }

}