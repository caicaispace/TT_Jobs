<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/7/18
 * Time: 9:26
 */

namespace Core\Swoole;

use Core\Component\Error\Trigger;
use Core\Component\Logger;

/**
 * Class AutoReload
 *
 * @package Core\Swoole
 */
class AutoReload
{
    /**
     * @var int
     */
    private $_inotify;
    private $_serverPid;

    private $_reloadFileTypes  = ['.php' => true];
    private $_watchingFiles    = [];
    private $_rootDirs         = [];
    private $_afterSomeSeconds = 10;
    private $_events;

    private $_reloading = false;

    /**
     * AutoReload constructor.
     *
     * @param $serverPid
     */
    function __construct($serverPid = null)
    {
        if (!extension_loaded('inotify')) {
            return false;
//            exit("Please install inotify extension.\n");
        }
        $this->_serverPid = $serverPid;
//        if (\posix_kill($serverPid, 0) === FALSE) {
//            Trigger::error("Process {$serverPid} not found.");
//        }
        $this->_inotify = \inotify_init();
        $this->_events  = IN_MODIFY | IN_DELETE | IN_CREATE | IN_MOVE;
        \swoole_event_add($this->_inotify, function ($ifd) {
            $events = \inotify_read($this->_inotify);
//            var_dump($events);
            if (!$events) {
                return;
            }
            foreach ($events as $event) {
                if (IN_IGNORED == $event['mask']) {
                    continue;
                } elseif (
                    IN_CREATE == $event['mask'] or
                    IN_DELETE == $event['mask'] or
                    IN_MODIFY == $event['mask'] or
                    IN_MOVED_TO == $event['mask'] or
                    IN_MOVED_FROM == $event['mask']) {
                    $fileType = strrchr($event['name'], '.');
                    if (!isset($this->_reloadFileTypes[$fileType])) { //非重启类型
                        continue;
                    }
                }
                if (!$this->_reloading) { // 正在reload，不再接受任何事件，冻结10秒
                    Logger::getInstance()->log("after 10 seconds reload the server");
                    \swoole_timer_after($this->_afterSomeSeconds * 1000, [$this, 'reload']); //有事件发生了，进行重启
                    $this->_reloading = true;
                }
            }
        });
    }

    function reload()
    {
        if ($this->_serverPid) {
            \posix_kill($this->_serverPid, SIGUSR1); // 向主进程发送信号
        } else {
            Server::getInstance()->getServer()->reload();
        }
        $this->clearWatch(); // 清理所有监听
        foreach ($this->_rootDirs as $root) { // 重新监听
            $this->watch($root);
        }
        $this->_reloading = false; // 继续进行reload
    }

    /**
     * 添加文件类型
     *
     * @param $type
     */
    function addFileType($type)
    {
        $type                                = trim($type, '.');
        $this->_reloadFileTypes['.' . $type] = true;
    }

    /**
     * 添加事件
     *
     * @param $inotifyEvent
     */
    function addEvent($inotifyEvent)
    {
        $this->_events |= $inotifyEvent;
    }

    /**
     * 清理所有inotify监听
     */
    function clearWatch()
    {
        foreach ($this->_watchingFiles as $wd) {
            \inotify_rm_watch($this->_inotify, $wd);
        }
        $this->_watchingFiles = [];
    }

    /**
     * @param      $dir
     * @param bool $root
     *
     * @return bool
     */
    function watch($dir, $root = true)
    {
        if (!\is_dir($dir)) { //目录不存在
            Trigger::error("[{$dir}] is not a directory.");
        }
        if (isset($this->_watchingFiles[$dir])) { //避免重复监听
            return false;
        }
        if ($root) { //根目录
            $this->_rootDirs[] = $dir;
        }
        $this->_watchingFiles[$dir] = inotify_add_watch($this->_inotify, $dir, $this->_events);
        $files                      = \scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' or $file == '..') {
                continue;
            }
            $path = $dir . '/' . $file;
            if (\is_dir($path)) { // 递归目录
                $this->watch($path, false);
            }
            $fileType = \strrchr($file, '.'); // 检测文件类型
            if (isset($this->_reloadFileTypes[$fileType])) {
                $this->_watchingFiles[$path] = \inotify_add_watch($this->_inotify, $path, $this->_events);
            }
        }
        return true;
    }

    function run()
    {
        \swoole_event_wait();
    }
}