<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/11/9
 * Time: 下午12:29
 */

namespace Core\Component\Error;


use Core\Conf\Config;
use Core\AbstractInterface\IErrorHandler;
use Core\AbstractInterface\IExceptionHandler;
use Core\Component\Di;
use Core\Component\SysConst;

/**
 * Class Trigger
 * @package Core\Component\Error
 */
class Trigger
{
    public static function error($msg, $file = null, $line = null, $errorCode = E_USER_ERROR, $trace = null)
    {
        $conf = Config::getInstance()->getConf("APP_DEBUG");
        if ($trace == null) {
            $trace = debug_backtrace();
        }
        $handler = Di::getInstance()->get(SysConst::ERROR_HANDLER);
        if (!$handler instanceof IErrorHandler) {
            $handler = new ErrorHandler();
        }
        $handler->handler($msg, $file, $line, $errorCode, $trace);
        if ($conf['DISPLAY_ERROR'] == true) {
            $handler->display($msg, $file, $line, $errorCode, $trace);
        }
        if ($conf['LOG'] == true) {
            $handler->log($msg, $file, $line, $errorCode, $trace);
        }
    }

    public static function exception(\Exception $exception)
    {
        $conf    = Config::getInstance()->getConf("APP_DEBUG");
        $handler = Di::getInstance()->get(SysConst::EXCEPTION_HANDLER);
        if (!$handler instanceof IExceptionHandler) {
            $handler = new ExceptionHandler();
        }
        $handler->handler($exception);
        if ($conf['DISPLAY_ERROR'] == true) {
            $handler->display($exception);
        }
        if ($conf['LOG'] == true) {
            $handler->log($exception);
        }
    }
}