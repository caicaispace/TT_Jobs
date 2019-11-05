<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/7
 * Time: 23:22
 */

namespace App\Jobs\Controller\Jobs;

use Core\AbstractInterface\AHttpController as Controller;
use Core\Http\Message\Status;
use Core\Swoole\Process\ProcessManager;
use Core\Swoole\Timer;
use Common\Process as ProcessTest;

/**
 * Class Process
 *
 * @package Jobs\Controller\Jobs
 */
class TestProcess extends Controller
{
    protected static $processName = 'swoole_process_test';
    private          $counter     = 0;

    function go()
    {
        go(function () {
            $ret = \Swoole\Coroutine\System::exec("php " . ROOT . "/test.php " . __FILE__);
            var_dump($ret);
        });
        go(function () {
            $ret = \Swoole\Coroutine\System::exec("php " . ROOT . "/test.php " . __FILE__);
            var_dump($ret);
        });
        go(function () {
            $ret = \Swoole\Coroutine\System::exec("php " . ROOT . "/test.php " . __FILE__);
            var_dump($ret);
        });
        $this->response()->writeJson(Status::CODE_OK, 'success');
    }

    function start()
    {
        ProcessManager::getInstance()->addProcess($this->processName, ProcessTest::class);
        $process = ProcessManager::getInstance()->getProcessByName($this->processName)->getProcess();
        $pid     = $process->start();
        $this->response()->write($this->counter++);
        $this->response()->write("process pid : {$pid}");
    }

    function writeData()
    {
        $data = 'processWriteData';
        $ret  = ProcessManager::getInstance()->writeByProcessName(self::$processName, $data);
        $this->response()->write("data : {$data}<br/>");
        $this->response()->write("result : {$ret}");
    }

    function getPid()
    {
        $pid = $this->_getPid();
        $this->response()->write("result : {$pid}");
    }

    function hasExit()
    {
        $result = $this->_hasExit();
        $this->response()->write("result : {$result}");
    }

    function kill()
    {
        if (!$pid = $this->_getPid()) {
            $this->response()->write("{$pid} :process dont find");
            return;
        }
        Timer::delay(1000, function () use ($pid) {
            \swoole_process::kill($pid);
        });
        $this->response()->write("{$pid} :process has killed");
    }

    function reboot()
    {
        ProcessManager::getInstance()->reboot(self::$processName);
    }

    private function _hasExit()
    {
        $pid = $this->_getPid();
        if (false === \swoole_process::kill($pid, 0)) {
            return false;
        }
        return true;
    }

    private function _getPid()
    {
        if (!$process = ProcessManager::getInstance()->getProcessByName(self::$processName)) {
            return null;
        }
        $pid = $process->getPid();
        return $pid;
    }
}