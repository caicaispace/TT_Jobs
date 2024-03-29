<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Controller\Jobs;

use App\Jobs\Logic\TaskLog as Logic;
use Core\AbstractInterface\ARESTController as Controller;

class TaskLog extends Controller
{
    public function GET_index()
    {
        $logic = new Logic();
        $logic->request()->setPage($this->getPageData());
        $logic->request()->setOrder(['id DESC']);
        if ($search = $this->request()->getQueryParam('search')) {
            $logic->request()->setWhere(['task_id' => $search]);
        }
        $ret = $logic->call('getList');
        if (! $ret->getStatus()) {
            $this->json()->error($ret->getMsg());
            return;
        }
        /* 展示字段 */
        $fields       = [
            'task_id'      => '任务 ID',
            'command'      => '执行命令',
            'output'       => '任务输出',
            //            'error'        => '错误信息',
            //            'status'       => '状态',
            'process_time' => '消耗时长/秒',
            'create_at'    => '添加时间',
        ];
        $responseData = $ret->getData();
        $this->json()
            ->setPage($ret->getPage())
            ->setListData($responseData)
            ->setFieldsMap($fields)
            ->setUniqueId('id')
            ->response();
    }
}
