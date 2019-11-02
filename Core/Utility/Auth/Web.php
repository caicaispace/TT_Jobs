<?php
/**
 * Created by PhpStorm.
 * User: safer
 * Date: 2018/6/27
 * Time: 22:27:51
 */

namespace Core\Utility\Auth;

use Core\Http\SessionFacade as Session;
use Core\conf\Config;
use think\Db;

/*
    权限认证类

    功能特性：

    1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
         $auth=new Auth();  $auth->check('规则名称','用户id')

    2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
         $auth=new Auth();  $auth->check('规则1,规则2','用户id','and')
         第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，
         表示用户值需要具备其中一个条件即可。默认为or

    3，一个用户可以属于多个用户组(tt_auth_group_access表 定义了用户所属用户组)。
        我们需要设置每个用户组拥有哪些规则(tt_auth_group 定义了用户组权限)

    4，支持规则表达式。
        在tt_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。
        如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。

*/

//数据库

/*

-- ----------------------------
-- think_auth_rule，规则表，
-- id:主键，name：规则唯一标识, title：规则中文名称 status 状态：为1正常，为0禁用，condition：规则表达式，为空表示存在就验证，不为空表示按照条件验证
-- ----------------------------

 DROP TABLE IF EXISTS `tt_auth_rule`;
CREATE TABLE `tt_auth_rule` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `name` char(80) NOT NULL DEFAULT '',
    `title` char(20) NOT NULL DEFAULT '',
    `type` char(80) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `condition` char(100) NOT NULL DEFAULT '',  # 规则附件条件,满足附加条件的规则,才认为是有效的规则
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- tt_auth_group 用户组表，
-- id：主键， title:用户组中文名称， rules：用户组拥有的规则id， 多个规则","隔开，status 状态：为1正常，为0禁用
-- ----------------------------

 DROP TABLE IF EXISTS `tt_auth_group`;
CREATE TABLE `tt_auth_group` (
    `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    `title` char(100) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `rules` char(80) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
-- tt_auth_group_access 用户组明细表
-- uid:用户id，group_id：用户组id
-- ----------------------------

DROP TABLE IF EXISTS `tt_auth_group_access`;
CREATE TABLE `tt_auth_group_access` (
    `uid` mediumint(8) unsigned NOT NULL,
    `group_id` mediumint(8) unsigned NOT NULL,
    UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
    KEY `uid` (`uid`),
    KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

*/

/**
 * Class newWeb
 * @package Core\Utility\Auth
 */
class Web
{
    const AUTH_ON = true;
    const AUTH_OFF = false;

    const REAL_TIME_AUTH = 1;
    const LOGIN_TIME_AUTH = 2;

    protected $_config = [
        'auth_on'            => self::AUTH_ON,          // 认证开关
        'auth_type'          => self::REAL_TIME_AUTH,   // 认证方式，1为实时认证；2为登录认证。
        'auth_group'         => 'auth_group',           // 用户组数据表名
        'auth_group_access'  => 'auth_group_access',    // 用户-用户组关系表
        'auth_rule'          => 'auth_rule',            // 权限规则表
        'auth_user'          => 'admin',                // 用户信息表
        'auth_user_id_field' => 'id',                   // 用户表ID字段名
    ];

    public function __construct()
    {
        if (Config::getInstance()->getConf('auth')) {
            $this->_config = array_merge($this->_config, Config::getInstance()->getConf('auth'));
        }
    }

    /**
     * @param string|array  $name      需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param int           $uid       认证用户的id
     * @param int           $type
     * @param string        $relation  如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @param string        $mode      执行check的模式
     * @return bool                    通过验证返回true;失败返回false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check($name, $uid, $type, $relation = 'or', $mode = 'url')
    {
        if (!$this->_config['auth_on']) {
            return true;
        }

        $authList = $this->getAuthList($uid, $type);
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = [];
        if ($mode === 'url') {
            $REQUEST = unserialize(strtolower(serialize(\Core\Http\Request::getInstance()->getRequestParam())));
        }

        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ($mode === 'url' && $query != $auth) {
                parse_str($query, $param); // 解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth      = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    $list[] = $auth;
                }
            } elseif (in_array($auth, $name)) {
                $list[] = $auth;
            }
        }

        if ($relation === 'or' && !empty($list)) {
            return true;
        }

        $diff = array_diff($name, $list);

        if ($relation === 'and' && empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * @param int $uid
     * @return array|bool array(array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'))
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroups($uid)
    {
        static $groups = [];

        if (isset($groups[$uid])) {
            return $groups[$uid];
        }

        $user_groups  = Db::name($this->_config['auth_group_access'])
            ->alias('a')
            ->where('a.uid', $uid)
            ->where('g.status', 1)
            ->join($this->_config['auth_group'] . ' g', "a.group_id = g.id")
            ->field('uid,group_id,title,rules')
            ->select();
        $groups[$uid] = $user_groups ?: [];
        return $groups[$uid];
    }

    /**
     * @param $uid
     * @param $type
     * @return array|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList = [];

        $t = implode(',', (array)$type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }

        if (
            $this->_config['auth_type'] == self::LOGIN_TIME_AUTH
            &&
            Session::has('_AUTH_LIST_' . $uid . $t)
        ) {
            return Session::find('_AUTH_LIST_' . $uid . $t);
        }

        // 读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids    = []; // 保存用户所属用户组设置的所有权限规则ID
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }

        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }

        $map = [
            ['id', 'in', $ids],
            ['type', '=', $type],
            ['status', '=', 1]
        ];

        // 读取用户组所有权限规则
        $rules = Db::name($this->_config['auth_rule'])
            ->where($map)
            ->field('condition,name')
            ->select();

        // 循环规则，判断结果。
        $authList = [];
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) { // 根据condition进行验证
                $user = $this->getUserInfo($uid); // 获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                // dump($command); // debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = strtolower($rule['name']);
                }
            } else {
                // 只要存在就记录
                $authList[] = strtolower($rule['name']);
            }
        }

        $_authList[$uid . $t] = $authList;

        if ($this->_config['auth_type'] == 2) {
            Session::set('_AUTH_LIST_' . $uid . $t, $authList);
        }
        return array_unique($authList);
    }

    /**
     * @param $uid
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getUserInfo($uid)
    {
        static $userInfo = [];
        if (!isset($userInfo[$uid])) {
            $userInfo[$uid] = Db::name($this->_config['auth_user'])
                ->where((string)$this->_config['auth_user_id_field'], $uid)
                ->find();
        }
        return $userInfo[$uid];
    }
}