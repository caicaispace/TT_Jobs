<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Logic;

use App\Jobs\Model\Admin as Model;
use Core\AbstractInterface\ALogic;
use Exception;

/**
 * Class Admin.
 */
class Admin extends ALogic
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

        $list = $ret->toArray();
        $list = array_map(function ($item) {
            $item['last_ip'] = long2ip($item['last_ip']);
            return $item;
        }, $list);

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
        if (! $requestData = $this->request()->getData()) {
            return $this->response()->error();
        }
        $model = new Model();
        if (! $ret = $model->save($requestData)) {
            return $this->response()->error();
        }
        $requestData = $model->toArray();
        return $this->response()
            ->setData($requestData)
            ->success();
    }

    public function update()
    {
        if (! $id = $this->request()->getId()) {
            return $this->response()->error();
        }
        if (! $requestData = $this->request()->getData()) {
            return $this->response()->error();
        }
        if (! $model = (new Model())->get($id)) {
            return $this->response()->error();
        }
        try {
            if (! $ret = $model->save($requestData)) {
                return $this->response()->error();
            }
        } catch (Exception $e) {
            return $this->response()->error($e->getMessage());
        }
        return $this->response()
            ->success();
    }

    public function delete()
    {
        if (! $id = $this->request()->getId()) {
            return $this->response()->error();
        }
        if (! $model = (new Model())->get($id)) {
            return $this->response()->error();
        }
        $model->setAttr('is_del', Model::DELETED);
        if (! $ret = $model->save()) {
            return $this->response()->error();
        }
        return $this->response()
            ->success();
    }
}
