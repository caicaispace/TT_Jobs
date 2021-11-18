<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Event;

use App\Jobs\Model\AuthAccessLog as AuthAccessLogModel;
use Core\Http\Message\Status as HttpStatus;
use Core\Http\Request;
use Core\Http\Response;
use Core\Utility\Auth\Web as Auth;

/**
 * Class OnHttpDispatcher.
 */
class OnHttpDispatcher
{
    /**
     * 权限检测.
     *
     * @param string $targetControllerClass
     * @param string $action
     *
     * @return bool
     */
    public static function auth(Request $request, Response $response, $targetControllerClass, $action)
    {
//        return true;
        $authSession = $request->session()->get('auth');
        if ($authSession && $authSession['username'] == 'admin') {
            return true;
        }
        $targetControllerClass = ltrim($targetControllerClass, '\\');
        $path                  = explode('\\', $targetControllerClass);
        if (count($path) >= 4 && $path[3] == 'Index') { // 首页不检测权限
            return true;
        }
        unset($path[2]);
//        $authCheckName = $path[2] . '\\' . $path[3] . '\\' . $action;
        $authCheckName = join('\\', array_slice($path, 0, 4));
        try {
            if ((new Auth())->check($authCheckName, $authSession['id'], 'Jobs') === false) {
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
     * @param string $targetControllerClass
     * @param string $action
     */
    public static function accessLog(Request $request, Response $response, $targetControllerClass, $action)
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

    private static function _error(Response $response)
    {
        $response->writeJson(HttpStatus::CODE_FORBIDDEN, ['message' => '权限错误', 'status' => 0]);
    }
}
