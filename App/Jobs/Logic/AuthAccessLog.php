<?php

declare(strict_types=1);
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Logic;

use App\Jobs\Model\AuthAccessLog as Model;
use Core\AbstractInterface\ALogic;
use Exception;

/**
 * Class AuthAccessLog.
 */
class AuthAccessLog extends ALogic
{
    public function getList()
    {
        $model = new Model();
        $model->where('id', '>', 0);
        /* 分页 */
        if ($page = $this->request()->getPage()) {
            if ($page['is_first']) {
                $page['total'] = $model->count('id') | 0;
            }
            $model = $model->limit($page['start'], $page['limit']);
            $this->response()->setPage($page);
        }
        /* 查询 */
        if ($where = $this->request()->getWhere()) {
            $where = 0 < sizeof($where) ? join(' and ', $where) : array_shift($where);
            $model = $model->where($where);
        }
        /* 排序 */
        if ($order = $this->request()->getOrder()) {
            $model = $model->order($order);
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
        if (! $id = $this->request()->getId()) {
            return $this->response()->error();
        }
        if (! $model = (new Model())->get($id)) {
            return $this->response()->error();
        }
        $responseData = $model->toArray();
        return $this->response()
            ->setData($responseData)
            ->success();
    }

    public function create()
    {
        if (! $responseData = $this->request()->getData()) {
            return $this->response()->error();
        }
        $model = new Model();
        if (! $ret = $model->save($responseData)) {
            return $this->response()->error();
        }
        $responseData = $model->toArray();
        return $this->response()
            ->setData($responseData)
            ->success();
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
