<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Controller;

use App\Jobs\Model\Admin as AdminModel;
use Core\AbstractInterface\AHttpController as Controller;
use Core\Http\Message\Status as HttpStatus;
use Core\Http\SessionFacade as Session;

/**
 * Class Index.
 */
class Index extends Controller
{
    public function index()
    {
        $this->response()->withHeader('Content-type', 'text/html;charset=utf-8');
        if ($this->_auth() === false) {
            $this->response()->write(file_get_contents(ROOT . '/Public/login.html'));
            return;
        }
        $this->response()->write(file_get_contents(ROOT . '/Public/index.html'));
    }

    public function login()
    {
        $responseError   = [
            'status'  => 0,
            'message' => 'error',
        ];
        $responseSuccess = [
            'status'  => 1,
            'message' => 'success',
        ];
        if (null === $username = $this->request()->getParsedBody('username')) {
            return $this->response()->writeJson(HttpStatus::CODE_OK, $responseError);
        }
        if (null === $password = $this->request()->getParsedBody('password')) {
            return $this->response()->writeJson(HttpStatus::CODE_OK, $responseError);
        }
        $model = new AdminModel();
        $model = $model->field(['id', 'username', 'zh_username', 'email', 'last_login', 'last_ip']);
        $model = $model->where('username', '=', $username);
        $model = $model->where('password', '=', md5($password));
        try {
            if (! $ret = $model->find()) {
                return $this->response()->writeJson(HttpStatus::CODE_OK, $responseError);
            }
            Session::set('auth', $ret->toArray());
            $header   = $this->request()->getSwooleRequest()->header;
            $clientIp = isset($header['x-real-ip']) ? $header['x-real-ip'] : null; // nginx
            if (! $clientIp) {
                $clientIp = $this->request()->getSwooleRequest()->server['remote_addr'];  // swoole_http_server
            }
            $ret->save([
                'last_ip'    => ip2long($clientIp),
                'last_login' => time(),
            ]);
        } catch (\Exception $e) {
            return $this->response()->writeJson(HttpStatus::CODE_OK, $responseError);
        }
        return $this->response()->writeJson(HttpStatus::CODE_OK, $responseSuccess);
    }

    public function loginOut()
    {
        $responseSuccess = [
            'status'  => 1,
            'message' => 'success',
        ];
        Session::delete('auth');
        return $this->response()->writeJson(HttpStatus::CODE_OK, $responseSuccess);
    }

    public function getSession()
    {
        $session = $this->request()->session()->get('auth');
        return $this->response()->writeJson(HttpStatus::CODE_OK, $session);
    }

    public function getUsers()
    {
        $users = (new AdminModel())
            ->field(['id', 'username', 'zh_username'])
            ->select();
        return $this->response()->writeJson(HttpStatus::CODE_OK, $users);
    }

    private function _auth()
    {
        if (Session::find('auth') === null) {
            return false;
        }
        return true;
    }
}
