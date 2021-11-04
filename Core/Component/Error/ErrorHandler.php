<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Error;

use Core\AbstractInterface\IErrorHandler;
use Core\Component\Logger;
use Core\Http\Request;
use Core\Http\Response;

class ErrorHandler implements IErrorHandler
{
    public function handler($msg, $file = null, $line = null, $errorCode = null, $trace)
    {
    }

    public function display($msg, $file = null, $line = null, $errorCode = null, $trace)
    {
        //判断是否在HTTP模式下
        if (Request::getInstance()) {
            Response::getInstance()->write(nl2br($msg) . " in file {$file} line {$line}");
        } else {
            Logger::getInstance('error')->console($msg . " in file {$file} line {$line}", false);
        }
    }

    public function log($msg, $file = null, $line = null, $errorCode = null, $trace)
    {
        Logger::getInstance('error')->log($msg . " in file {$file} line {$line}");
    }
}
