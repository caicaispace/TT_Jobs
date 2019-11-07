<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/7/9
 * Time: 0:31:07
 */

namespace App\Jobs\Event;

use Core\Http\Message\Status as HttpStatus;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utility\Auth\Web as Auth;
use App\Jobs\Model\AuthAccessLog as AuthAccessLogModel;
use function FastRoute\cachedDispatcher;

/**
 * Class onHttpDispatcher
 *
 * @package Jobs\Event
 */
class onHttpDispatcher
{
    /**
     * 权限检测
     *
     * @param Request  $request
     * @param Response $response
     * @param string   $targetControllerClass
     * @param string   $action
     *
     * @return bool
     */
    static function auth(Request $request, Response $response, $targetControllerClass, $action)
    {
//        return true;
        $authSession = $request->session()->get('auth');
        if ($authSession && $authSession['username'] == 'admin') {
            return true;
        }
        $targetControllerClass = ltrim($targetControllerClass,'\\');
        $path = explode('\\', $targetControllerClass);
        if (count($path) >= 4 && $path[3] == 'Index') { // 首页不检测权限
            return true;
        }
        unset($path[2]);
//        $authCheckName = $path[2] . '\\' . $path[3] . '\\' . $action;
        $authCheckName = join('\\', array_slice($path, 0, 4));
        try {
            if (false === (new Auth)->check($authCheckName, $authSession['id'], 'Jobs')) {
                self::_error($response);
                return false;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo $e->getFile();
            echo $e->getLine();
        }
        return true;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param string   $targetControllerClass
     * @param string   $action
     */
    static function accessLog(Request $request, Response $response, $targetControllerClass, $action)
    {
        if (null === $authSession = $request->session()->get('auth')) {
            return;
        }
        $path = explode('\\', $targetControllerClass);
        if (count($path) >= 4 && $path[3] == 'Index') {
            return;
        }

        $accessPath = $targetControllerClass . '\\' . $action;
        $accessData = $request->getSwooleRequest()->rawContent();

        $data = [
            'uid'         => $authSession['id'],
            'access_path' => $accessPath,
            'access_data' => $accessData,
        ];

        AuthAccessLogModel::create($data);
    }

    /**
     * @param Response $response
     */
    static private function _error(Response $response)
    {
        $response->writeJson(HttpStatus::CODE_FORBIDDEN, ['message' => '权限错误', 'status' => 0]);
    }
}