<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Controller\Jobs;

use App\Jobs\Logic\AuthAccessLog as Logic;
use Core\AbstractInterface\ARESTController as Controller;

/**
 * Class AuthAccessLog.
 */
class AuthAccessLog extends Controller
{
    public function GET_index()
    {
        $logic = new Logic();
        $logic->request()->setPage($this->getPageData());
        $logic->request()->setOrder(['id DESC']);
        if ($this->request()->session()->get('auth')['username'] != 'admin') {
            $uid = $this->request()->session()->get('auth')['id'];
            $logic->request()->setWhere(["uid = {$uid}"]);
        }
        $ret = $logic->call('getList');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        /* 展示字段 */
        $fields       = [
            'uid'         => 'uid',
            'access_path' => '访问路径',
            'access_data' => '提交数据',
            'create_at'   => '访问时间',
        ];
        $responseData = $ret->getData();
        $this->json()
            ->setPage($ret->getPage())
            ->setListData($responseData)
            ->setFieldsMap($fields)
            ->setUniqueId('id')
            ->response();
    }

    public function GET_info()
    {
        if (! $id = $this->request()->getServerParam('id')) {
            $this->response()->write('操作失败');
            return;
        }
        $authGroupLogic = new Logic();
        $authGroupLogic->request()->setId($id);
        $ret = $authGroupLogic->call('getInfo');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $responseData = $ret->getData();
        $this->json()
            ->setRowData($responseData)
            ->response();
    }

    public function POST_index()
    {
        if (! $requestData = $this->request()->getPostData()) {
            $this->json()->error();
            return;
        }
        $logic = new Logic();
        $logic->request()->setData($requestData);
        $ret = $logic->call('create');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $responseData = $ret->getData();
        $this->json()
            ->setUniqueId('id')
            ->setRowData($responseData)
            ->response();
    }

    public function PUT_index()
    {
    }

    public function PATCH_index()
    {
    }

    public function DELETE_index()
    {
    }
}
