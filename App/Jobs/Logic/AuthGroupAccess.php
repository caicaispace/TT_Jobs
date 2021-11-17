<?php
/**
 * @link https://github.com/TTSimple/TT_Jobs
 */
namespace App\Jobs\Logic;

use App\Jobs\Model\AuthGroupAccess as Model;
use Core\AbstractInterface\ALogic;
use Exception;

/**
 * Class AuthGroupAccess.
 */
class AuthGroupAccess extends ALogic
{
    public function getList()
    {
    }

    public function getInfo()
    {
        if (! $id = $this->request()->getId()) {
            return $this->response()->error();
        }
        try {
            $model = (new Model())->field('group_concat(`group_id`) as groups')->where('uid', $id)->find();
        } catch (Exception $e) {
            return $this->response()->error($e->getMessage());
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
        if (! $uid = $this->request()->getExtend('uid')) {
            return $this->response()->error();
        }
        if (is_string($requestData['group_id'])) {
            $requestData = explode(',', $requestData['group_id']);
            foreach ($requestData as $k => $group_id) {
                $requestData[$k] = [
                    'uid'      => $uid,
                    'group_id' => $group_id,
                ];
            }
        }
        $model = new Model();
        if (! $ret = $model->save($requestData)) {
            return $this->response()->error();
        }
        $responseData = $model->toArray();
        return $this->response()
            ->setData($responseData)
            ->success();
    }

    public function update()
    {
        if (! $uid = $this->request()->getExtend('uid')) {
            return $this->response()->error();
        }
        if (! $requestData = $this->request()->getData()) {
            return $this->response()->error();
        }
        if (is_string($requestData['group_id'])) {
            $requestData = explode(',', $requestData['group_id']);
            foreach ($requestData as $k => $group_id) {
                $requestData[$k] = [
                    'uid'      => $uid,
                    'group_id' => $group_id,
                ];
            }
        }
        try {
            (new Model())->where('uid', $uid)->delete();
        } catch (Exception $e) {
            return $this->response()->error($e->getMessage());
        }
        if (! $ret = (new Model())->insertAll($requestData)) {
            return $this->response()->error();
        }
        return $this->response()
            ->success();
    }

    public function delete()
    {
    }
}
