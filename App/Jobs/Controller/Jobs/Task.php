<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Controller\Jobs;

use App\Jobs\Logic\Task as Logic;
use Core\AbstractInterface\ARESTController as Controller;
use Core\Http\Message\Status;
use Core\Swoole\Server;

/**
 * Class Task.
 */
class Task extends Controller
{
    public function GET_index()
    {
        $logic = new Logic();
        $logic->request()->setPage($this->getPageData());
        if ($groupId = $this->request()->getQueryParam('group_id')) {
            $logic->request()->setWhere(["group_id = {$groupId}"]);
        }
        if ($search = $this->request()->getQueryParam('search')) {
            $logic->request()->setExtend(['search' => $search]);
        }
        $logic->request()->setOrder(['id DESC']);
        $ret = $logic->call('getList');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        /* 展示字段 */
        $fields       = [
            'id'            => 'ID',
            'task_name'     => '任务名称',
            'description'   => '任务说明',
            'user_id'       => '添加人',
            'cron_spec'     => '时间表达式',
            'execute_times' => '累计执行次数',
            'prev_time'     => '上次执行时间',
            'create_at'     => '添加时间',
            'status'        => '状态',
        ];
        $responseData = $ret->getData();
        $this->json()
            ->setPage($ret->getPage())
            ->setListData($responseData)
            ->setFieldsMap($fields)
            ->setUniqueId('id')
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

    public function PUT_Index()
    {
        $responseData = 'PUT';
        $this->response()->writeJson(Status::CODE_OK, $responseData);
    }

    public function PATCH_index()
    {
        if (! $id = $this->request()->getServerParam('id')) {
            $this->json()->error();
            return;
        }
        if (! $requestData = $this->request()->getPostData()) {
            $this->json()->error();
            return;
        }
        $logic = new Logic();
        $logic->request()->setId($id);
        $logic->request()->setData($requestData);
        $ret = $logic->call('update');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        $this->json()->success();
    }

    public function DELETE_index()
    {
    }

    public function GET_runTest()
    {
        if (! $command = $this->request()->getQueryParam('command')) {
            $this->json()->error();
            return;
        }
        $process = new \swoole_process(function (\swoole_process $process) use ($command) {
            [$runPath, $filePath] = explode(' ', $command);
            $process->exec($runPath, [$filePath]);
        }, true, 2);
        $process->start();
        Server::getInstance()->getServer()->addProcess($process);
        $responseData = $process->read();
        \swoole_process::wait(true);
        $this->json()
            ->setRowData($responseData)
            ->response();
    }
}
