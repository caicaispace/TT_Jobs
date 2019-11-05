<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/7/10
 * Time: 23:21:50
 */

namespace App\Jobs\Logic;

use Core\AbstractInterface\ALogic;
use App\Jobs\Model\AuthGroupAccess as Model;
use Exception;

/**
 * Class AuthGroupAccess
 *
 * @package Jobs\Logic
 */
class AuthGroupAccess extends ALogic
{

    function getList()
    {
    }

    function getInfo()
    {
        if (!$id = $this->request()->getId()) {
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

    function create()
    {
        if (!$requestData = $this->request()->getData()) {
            return $this->response()->error();
        }
        if (!$uid = $this->request()->getExtend('uid')) {
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
        $model = new Model;
        if (!$ret = $model->save($requestData)) {
            return $this->response()->error();
        }
        $responseData = $model->toArray();
        return $this->response()
            ->setData($responseData)
            ->success();
    }

    function update()
    {
        if (!$uid = $this->request()->getExtend('uid')) {
            return $this->response()->error();
        }
        if (!$requestData = $this->request()->getData()) {
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
            (new Model)->where('uid', $uid)->delete();
        } catch (Exception $e) {
            return $this->response()->error($e->getMessage());
        }
        if (!$ret = (new Model)->insertAll($requestData)) {
            return $this->response()->error();
        }
        return $this->response()
            ->success();
    }

    function delete()
    {
    }
}