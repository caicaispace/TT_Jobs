<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2017/11/9
 * Time: 下午7:05
 */

namespace Core\Component\Error;


use Core\AbstractInterface\IExceptionHandler;
use Core\Component\Logger;
use Core\Http\Request;
use Core\Http\Response;

class ExceptionHandler implements IExceptionHandler
{

    function handler(\Exception $exception)
    {
    }

    function display(\Exception $exception)
    {
        if (Request::getInstance()) {
            Response::getInstance()->write(nl2br($exception->getMessage() . $exception->getTraceAsString()));
        } else {
            Logger::getInstance('error')->console($exception->getMessage() . $exception->getTraceAsString(), false);
        }
    }

    function log(\Exception $exception)
    {
        Logger::getInstance('error')->log($exception->getMessage() . " " . $exception->getTraceAsString());
    }
}