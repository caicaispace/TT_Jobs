<?php

namespace Core\AbstractInterface;


interface ILoggerWriter
{
    static function writeLog($obj, $logCategory, $timeStamp);
}