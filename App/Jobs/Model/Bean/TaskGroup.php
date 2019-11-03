<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/5/16
 * Time: 17:26
 */

namespace App\Jobs\Model\Bean;


use Core\Component\Spl\SplBean;

/**
 * Class TaskGroup
 *
 * @package Jobs\Model\Bean
 */
class TaskGroup extends SplBean
{
    public $id;
    public $user_id;
    public $group_name;
    public $description;
    public $is_del;
    public $create_at;
    public $update_at;

    protected function initialize()
    {
        // TODO: Implement initialize() method.
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->group_name;
    }

    /**
     * @param mixed $group_name
     */
    public function setGroupName($group_name)
    {
        $this->group_name = $group_name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getisDel()
    {
        return $this->is_del;
    }

    /**
     * @param mixed $is_del
     */
    public function setIsDel($is_del)
    {
        $this->is_del = $is_del;
    }

    /**
     * @return mixed
     */
    public function getCreateAt()
    {
        return $this->create_at;
    }

    /**
     * @param mixed $create_at
     */
    public function setCreateAt($create_at)
    {
        $this->create_at = $create_at;
    }

    /**
     * @return mixed
     */
    public function getUpdateAt()
    {
        return $this->update_at;
    }

    /**
     * @param mixed $update_at
     */
    public function setUpdateAt($update_at)
    {
        $this->update_at = $update_at;
    }
}
