<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\AbstractInterface;

interface ILoggerWriter
{
    public static function writeLog($obj, $logCategory, $timeStamp);
}
