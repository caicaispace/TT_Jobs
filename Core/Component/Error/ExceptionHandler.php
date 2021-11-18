<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component\Error;

use Core\AbstractInterface\IExceptionHandler;
use Core\Component\Logger;
use Core\Http\Request;
use Core\Http\Response;

class ExceptionHandler implements IExceptionHandler
{
    public function handler(\Exception $exception)
    {
    }

    public function display(\Exception $exception)
    {
        if (Request::getInstance()) {
            Response::getInstance()->write(nl2br($exception->getMessage() . $exception->getTraceAsString()));
        } else {
            Logger::getInstance('error')->console($exception->getMessage() . $exception->getTraceAsString(), false);
        }
    }

    public function log(\Exception $exception)
    {
        Logger::getInstance('error')->log($exception->getMessage() . ' ' . $exception->getTraceAsString());
    }
}
