<?php

namespace Core\Vendor\Tools;

use Core\Http\Response;
use Core\Http\Message\Status;

/**
 * Class Json
 * @package common\Library\Tools
 */
class HttpResponseJsonSchema
{
    private $uniqueId = null;

    private $rowData = [];

    private $listData = [];

    private $fieldsMap = [];

    private $page = []; //array('current'=>1,'total_items'=>10);

    private static $instance;

    static function getInstance()
    {
        // if (!isset(self::$instance)) {
        //     self::$instance = new self();
        // }
        // return self::$instance;
        return new self();
    }

    /**
     * 设置唯一ID 字段名
     * @param $id
     * @return $this
     */
    public function setUniqueId($id)
    {
        $this->uniqueId = $id;
        return $this;
    }

    /**
     * 设置info数据
     * @param $row
     * @return $this
     */
    public function setRowData($row)
    {
        $this->rowData = $row;
        return $this;
    }

    /**
     * 设置数据列表
     * @param $list
     * @return $this
     */
    public function setListData($list)
    {
        $this->listData = $list;
        return $this;
    }

    /**
     * 设置分页
     * @param $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * 设置字段映射
     * @param $fields
     * @return $this
     */
    public function setFieldsMap($fields)
    {
        $this->fieldsMap = $fields;
        return $this;
    }

    /**
     * 回复数据
     * @param string $message
     */
    public function response($message = '操作成功')
    {
        $response = [
            'info' => $this->rowData,
            'list' => $this->listData,
            'page' => $this->page,
            'options' => [
                'uniqueId' => $this->uniqueId,
                'fields'   => $this->fieldsMap,
            ]
        ];
        $response['message'] = $message;
        $this->send($response);
    }

    /**
     * 操作成功，返回json数据
     * @param string|array $data 需要返回的数据，可以覆盖任意默认参数字符串则认为是提示信息
     */
    public function success($data = '操作成功')
    {
        if (is_string($data)) {
            $this->send(['message' => $data]);
        } else {
            $this->send($data);
        }
    }

    /**
     * 操作失败，返回json数据
     * @param  string  $errorMsg 如果是数组，当任务参数处理，字符串则认为是错误信息
     * @param  integer $errorCode
     */
    public function error($errorMsg = '', $errorCode = 400)
    {
        $defaultMsg = '非法操作';
        $errorMsg   = $errorMsg ?: $defaultMsg;
        if (is_array($errorMsg)) {
            if (!isset($errorMsg['message'])) {
                $errorMsg['message'] = $defaultMsg;
            }
            $this->send(array_merge($errorMsg, ['status' => 0]));
        } else {
            $this->send([
                'step'      => $errorMsg,
                'message'   => is_numeric($errorMsg) ? $defaultMsg : $errorMsg,
                'errorCode' => $errorCode,
                'status'    => 0
            ]);
        }
    }

    private function send($data)
    {
        if (isset($data['message'])
            && (is_array($data['message']) || is_object($data['message']))) {
            $errors = [];
            foreach ($data['message'] as $message) {
                $errors[] = $message->getMessage();
            }
            if (count($errors) == 1) {
                $data['message'] = $errors[0];
            } else {
                $data['message'] = $errors;
            }
        }
        $response = [
            'info' => [],  //单条数据信息
            'list' => [],  //列表信息
            'page' => [],  //分页信息
            'options' => [    //相关配置
                'uniqueId' => null  //数据默认唯一索引 前端已自动默认为 id
            ],
            'errorCode' => 0, //错误代码
            'message'   => '', //信息
            'status'    => 1  //操作状态
        ];
        $response = array_merge($response, $data);
        Response::getInstance()->writeJson(Status::CODE_OK, $response);
    }
}
