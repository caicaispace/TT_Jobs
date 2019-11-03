<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/27
 * Time: 18:45
 */

namespace App\Jobs\Controller\Jobs;

use Core\AbstractInterface\ARESTController as Controller;
use Core\Http\Message\Status;
use App\Jobs\Logic\Admin as Logic;
use App\Jobs\Logic\AuthGroupAccess as AuthGroupAccessLogic;

/**
 * Class Admin
 *
 * @package Jobs\Controller\Jobs
 */
class Admin extends Controller
{
    function GET_index()
    {
        $logic = new Logic;
        $logic->request()->setPage($this->getPageData());
        $logic->request()->setOrder(['id DESC']);
        $ret = $logic->call('getList');
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        /* 展示字段 */
        $fields       = [
            'id'         => 'ID',
            'username'   => '用户名',
            'email'      => '邮箱',
            'last_login' => '最后登录时间',
            'last_ip'    => '最后登录IP',
            'status'     => '状态',
            'create_at'  => '创建时间',
            'update_at'  => '更新时间',
        ];
        $responseData = $ret->getData();
        $this->json()
            ->setPage($ret->getPage())
            ->setListData($responseData)
            ->setFieldsMap($fields)
            ->setUniqueId('id')
            ->response();
    }

    function GET_info()
    {
        if (!$id = $this->request()->getServerParam('id')) {
            $this->response()->write('操作失败');
            return;
        }
        $authGroupLogic = new AuthGroupAccessLogic;
        $authGroupLogic->request()->setId($id);
        $ret = $authGroupLogic->call('getInfo');
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $responseData = $ret->getData();
        $this->json()
            ->setRowData($responseData)
            ->response();
    }

    function POST_index()
    {
        if (!$requestData = $this->request()->getPostData()) {
            $this->json()->error();
            return;
        }
        $logic = new Logic;
        $logic->request()->setData($requestData);
        $ret = $logic->call('create');
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $responseData = $ret->getData();
        if ($groups = $this->request()->getPostData('groups')) {
            $authGroupLogic = new AuthGroupAccessLogic;
            $authGroupLogic->request()->setExtend(['uid' => $responseData['id']]);
            $authGroupLogic->request()->setData(['group_id' => $groups]);
            $ret = $authGroupLogic->call('update');
            if (!$ret->getStatus()) {
                $this->json()->error($ret->getMsg());
                return;
            }
        }
        $this->json()
            ->setUniqueId('id')
            ->setRowData($responseData)
            ->response();
    }

    function PUT_index()
    {
        $responseData = 'PUT';
        $this->response()->writeJson(Status::CODE_OK, $responseData);
    }

    function PATCH_index()
    {
        if (!$id = $this->request()->getServerParam('id')) {
            $this->json()->error();
            return;
        }
        if (!$requestData = $this->request()->getPostData()) {
            $this->json()->error();
            return;
        }
        $logic = new Logic;
        $logic->request()->setId($id);
        $logic->request()->setData($requestData);
        $ret = $logic->call('update');
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        if ($groups = $this->request()->getPostData('groups')) {
            $authGroupLogic = new AuthGroupAccessLogic;
            $authGroupLogic->request()->setExtend(['uid' => $id]);
            $authGroupLogic->request()->setData(['group_id' => $groups]);
            $ret = $authGroupLogic->call('update');
            if (!$ret->getStatus()) {
                $this->json()->error($ret->getMsg());
                return;
            }
        }
        $this->json()->success();
    }

    function DELETE_index()
    {
        if (!$id = $this->request()->getServerParam('id')) {
            $this->json()->error();
            return;
        }
        $logic = new Logic;
        $logic->request()->setId($id);
        $ret = $logic->call('delete');
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $this->json()->success();
    }
}