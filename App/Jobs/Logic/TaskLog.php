<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Logic;

use App\Jobs\Model\TaskLog as Model;
use Core\AbstractInterface\ALogic;
use Exception;

class TaskLog extends ALogic
{
    public function getList()
    {
        $model       = new Model();
        $clone_model = clone $model;
        $model->where('id', '>', 0);
        /* 查询 */
        if ($where = $this->request()->getWhere()) {
//            $where = 0 < sizeof($where) ? join(' and ', $where) : array_shift($where);
            $model = $model->where($where);
        }
        /* 排序 */
        if ($order = $this->request()->getOrder()) {
            $model = $model->order($order);
        }
        /* 分页 */
        if ($page = $this->request()->getPage()) {
            if ($page['is_first']) {
                $page['total'] = $clone_model->where($where)->count('id') | 0;
            }
            $model = $model->limit($page['start'], $page['limit']);
            $this->response()->setPage($page);
        }
        try {
            $ret = $model->select();
        } catch (Exception $e) {
            return $this->response()
                ->setMsg($e->getMessage())
                ->error();
        }
        $list         = $ret->toArray();
        $responseData = $list;
        return $this->response()
            ->setData($responseData)
            ->success();
    }

    public function getInfo()
    {
        // TODO: Implement getInfo() method.
    }

    public function create()
    {
        // TODO: Implement create() method.
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }
}
