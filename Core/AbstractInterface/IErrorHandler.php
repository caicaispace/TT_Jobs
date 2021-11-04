<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

interface IErrorHandler
{
    public function handler($msg, $file = null, $line = null, $errorCode = null, $trace);

    public function display($msg, $file = null, $line = null, $errorCode = null, $trace);

    public function log($msg, $file = null, $line = null, $errorCode = null, $trace);
}
