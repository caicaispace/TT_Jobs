<?php

namespace Core\AbstractInterface;

interface IErrorHandler
{
    function handler($msg, $file = null, $line = null, $errorCode = null, $trace);

    function display($msg, $file = null, $line = null, $errorCode = null, $trace);

    function log($msg, $file = null, $line = null, $errorCode = null, $trace);
}