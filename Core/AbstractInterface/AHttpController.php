<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/24
 * Time: 17:42
 */

namespace Core\AbstractInterface;

use Core\Conf\Config;
use Core\Http\Message\Status;
use Core\Http\Request;
use Core\Http\Response;
//use think\Template;

class AHttpController extends ABaseController
{
    /**
     * 模板引擎
     *
     * @var null
     */
    private $_templateEngine = null;

    public function __construct()
    {
        // 加载 topthink/think-template (v1.0.2) 开启
//        $tplConfig             = Config::getInstance()->getConf('TEMPLATE');
//        $this->_templateEngine = new Template($tplConfig);
    }

    function index()
    {
        $this->actionNotFound();
    }

    function request()
    {
        return Request::getInstance();
    }

    function response()
    {
        return Response::getInstance();
    }

    function responseError()
    {
        $this->response()->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
    }

    protected function onRequest($actionName)
    {
        return true;
    }

    protected function actionNotFound($actionName = null, $arguments = null)
    {
    }

    protected function afterAction()
    {
    }

//    /**
//     * 渲染模板文件
//     *
//     * @access public
//     *
//     * @param string $template 模板文件
//     * @param array  $vars     模板变量
//     * @param array  $config   模板参数
//     *
//     * @return void
//     */
//    public function display($template, $vars = [], $config = [])
//    {
//        // 由于ThinkPHP的模板引擎是直接echo输出到页面
//        // 这里我们打开缓冲区，让模板引擎输出到缓冲区，再获取到模板编译后的字符串
//
//        ob_start();
//        $this->_templateEngine->fetch($template, $vars, $config);
//        $content = ob_get_clean();
//        $this->response()->write($content);
//    }
//
//    /**
//     * 模板变量赋值
//     *
//     * @access public
//     *
//     * @param mixed $name
//     * @param mixed $value
//     */
//    public function assign($name, $value = '')
//    {
//        $this->_templateEngine->assign($name, $value);
//    }
}