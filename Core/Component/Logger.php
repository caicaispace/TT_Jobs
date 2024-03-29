<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Component;

use Core\AbstractInterface\ILoggerWriter;

class Logger
{
    private static $instance = [];
    private $logCategory     = 'default';

    public function __construct($logCategory)
    {
        $this->logCategory = $logCategory;
    }

    public static function getInstance($logCategory = 'default')
    {
        if (! isset(self::$instance[$logCategory])) {
            //这样做纯属为了IDE提示
            $instance                     = new static($logCategory);
            self::$instance[$logCategory] = $instance;
        } else {
            $instance = self::$instance[$logCategory];
        }
        return $instance;
    }

    public function log($obj)
    {
        $loggerWriter = Di::getInstance()->get(SysConst::LOGGER_WRITER);
        if ($loggerWriter instanceof ILoggerWriter) {
            $loggerWriter::writeLog($obj, $this->logCategory, time());
        } else {
            $obj = $this->objectToString($obj);
            /*
             * default method to save log
             */
            $str        = 'time : ' . date('y-m-d H:i:s') . ' message: ' . $obj . "\n";
            $filePrefix = $this->logCategory . '_' . date('ym');
            $filePath   = Di::getInstance()->get(SysConst::LOG_DIRECTORY) . "/{$filePrefix}.log";
            file_put_contents($filePath, $str, FILE_APPEND | LOCK_EX);
        }
        return $this;
    }

    public function console($obj, $saveLog = 1)
    {
        $obj = $this->objectToString($obj);
        echo $obj . "\n";
        if ($saveLog) {
            $this->log($obj);
        }
        return $this;
    }

    public function printStackTrace()
    {
        $array = debug_backtrace();
        unset($array[0]);
        $html = '';
        foreach ($array as $row) {
            $html .= $row['file'] . ' <---> ' . $row['line'] . ' <---> ' . $row['function'] . PHP_EOL;
        }
        echo $html;
    }

    private function objectToString($obj)
    {
        if (is_object($obj)) {
            if (method_exists($obj, '__toString')) {
                $obj = $obj->__toString();
            } elseif (method_exists($obj, 'jsonSerialize')) {
                $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                $obj = var_export($obj, true);
            }
        } elseif (is_array($obj)) {
            $obj = json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return $obj;
    }
}
