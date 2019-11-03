<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/7/9
 * Time: 22:52:32
 */

namespace App\Jobs\Controller\Jobs;

use Core\AbstractInterface\ARESTController as Controller;
use Core\Http\Message\Status;
use App\Jobs\Logic\AuthGroup as Logic;

class AuthGroup extends Controller
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
            'id'        => 'ID',
            'title'     => '标题',
            'status'    => '状态',
            'rules'     => '权限规则',
            'create_at' => '创建时间',
            'update_at' => '更新时间',
        ];
        $responseData = $ret->getData();
        $this->json()
            ->setPage($ret->getPage())
            ->setListData($responseData)
            ->setFieldsMap($fields)
            ->setUniqueId('id')
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
        $ret = $logic->create();
        if (!$ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $responseData = $ret->getData();
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