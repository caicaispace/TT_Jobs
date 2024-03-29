<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace Core\Conf;

use Core\Component\Spl\SplArray;

class Config
{
    protected $conf;
    private static $instance;

    public function __construct()
    {
        $this->conf = $this->appConf();
        $this->conf = $this->sysConf() + $this->conf;
        $this->conf = new SplArray($this->conf);
    }

    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function getConf($keyPath)
    {
        return $this->conf->get($keyPath);
    }

    /*
    * 在server启动以后，无法动态的去添加，修改配置信息（进程数据独立）
    */
    public function setConf($keyPath, $data)
    {
        $this->conf->set($keyPath, $data);
    }

    private function sysConf()
    {
        return $this->conf['SWOOLE'];
    }

    private function appConf()
    {
        $confPath = ROOT . '/App/' . APP_NAME . '/Conf';
        $initConf = parse_ini_file($confPath . '/config.ini');
        $envConf  = require_once $confPath . '/' . $initConf['APP_ENV'] . '.php';
        return array_merge($initConf, $envConf);
    }
}
