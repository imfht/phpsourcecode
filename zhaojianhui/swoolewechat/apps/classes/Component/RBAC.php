<?php

namespace App\Component;
/**
 * 权限认证类
 * 功能特性：
 * 1，是对规则进行认证，不是对节点进行认证。用户可以把节点当作规则名称实现对节点进行认证。
 *      $rbac=new RBAC();  $rbac->check('规则名称','用户id')
 * 2，可以同时对多条规则进行认证，并设置多条规则的关系（or或者and）
 *      $rbac=new RBAC();  $rbac->check('规则1,规则2','用户id','and')
 *      第三个参数为and时表示，用户需要同时具有规则1和规则2的权限。 当第三个参数为or时，表示用户值需要具备其中一个条件即可。默认为or
 * 3，一个用户可以属于多个用户组(sys_user_to_group表 定义了用户所属用户组)。我们需要设置每个用户组拥有哪些规则(sys_user_group 定义了用户组权限)
 *
 * 4，支持规则表达式。
 *      在sys_auth_rule 表中定义一条规则时，如果type为1， condition字段就可以定义规则表达式。 如定义{score}>5  and {score}<100  表示用户的分数在5-100之间时这条规则才会通过。
 * @category ORG
 * @package ORG
 * @subpackage Util
 * @author luofei614<weibo.com/luofei614>
 */
/**
 * 权限认证类.
 *
 * @see http://www.thinkphp.cn/topic/4029.html
 */
class RBAC
{
    const AUTH_TYPE_LIVE  = 1; //实时认证方式
    const AUTH_TYPE_LOGIN = 2; //登录认证方式
    //默认配置
    protected $_config = [
        'authOn'           => true, // 认证开关
        'authType'         => 1, // 认证方式，1为实时认证；2为登录认证。
    ];
    /**
     * 用户表模型.
     *
     * @var \App\Model\SysUser
     */
    private $userModel;
    /**
     * 权限规则表模型.
     *
     * @var \App\Model\SysAuthRule
     */
    private $authRuleModel;
    /**
     * 用户分组表模型.
     *
     * @var \App\Model\SysUserGroup
     */
    private $userGroupModel;
    /**
     * 用户关联分组表模型.
     *
     * @var \App\Model\SysUserToGroup
     */
    private $userToGroupModel;

    /**
     * 构造函数.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->_config['authType'] = self::AUTH_TYPE_LOGIN; //默认认证类别
        if ($config) {
            //可设置配置项 AUTH_CONFIG, 此配置项为数组。
            $this->_config = array_merge($this->_config, $config);
        }
        $this->userModel        = model('SysUser');
        $this->authRuleModel    = model('SysAuthRule');
        $this->userGroupModel   = model('SysUserGroup');
        $this->userToGroupModel = model('SysUserToGroup');
    }

    /**
     * 检查权限.
     *
     * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
     * @param uid  int           认证用户的id
     * @param mode string        执行check的模式
     * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
     * @param mixed $name
     * @param mixed $userId
     * @param mixed $type
     * @param mixed $mode
     * @param mixed $relation
     *
     * @return bool 通过验证返回true;失败返回false
     */
    public function check($name, $userId, $type = 1, $mode = 'url', $relation = 'or')
    {
        if (!$this->_config['authOn']) {
            return true;
        }
        $authList = $this->getAuthList($userId, $type); //获取用户需要验证的所有有效规则列表
        if (is_string($name)) {
            $name = strtolower($name);
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = unserialize(strtolower(serialize($_REQUEST)));
        }
        foreach ($authList as $auth) {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth      = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } elseif (in_array($auth, $name)) {
                $list[] = $auth;
            }
        }
        if ('or' == $relation and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation and empty($diff)) {
            return true;
        }

        return false;
    }

    /**
     * 根据用户id获取用户组,返回值为数组.
     *
     * @param  uid int     用户id
     * @param mixed $userId
     *
     * @return array 用户所属的用户组 array(
     *               array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     *               ...)
     */
    public function getGroupList($userId)
    {
        $userGroupList = $this->userGroupModel->getUserGroupListByUid($userId);
        return $userGroupList;
    }

    /**
     * 获得权限列表.
     *
     * @param int $userId  用户id
     * @param int $type
     */
    protected function getAuthList($userId, $type)
    {
        //保存用户验证通过的权限列表
        static $_authList        = [];
        $t                = implode(',', (array) $type);
        if (isset($_authList[$userId . $t])) {
            return $_authList[$userId . $t];
        }
        if ($this->_config['authType'] == self::AUTH_TYPE_LOGIN && isset($_SESSION['_AUTH_LIST_' . $userId . $t])) {
            return $_SESSION['_AUTH_LIST_' . $userId . $t];
        }
        //读取用户所属用户组
        $groups = $this->getGroupList($userId);
        //保存用户所属用户组设置的所有权限规则id
        $ids    = [];
        foreach ($groups as $g) {
            if ($g['ruleIds']){
                $ruleIds = unserialize($g['ruleIds']);
                $ruleIds && $ids = array_merge($ids, $ruleIds);
            }
        }
        //保存用户设置的权限规则ID
        $userData = $this->userModel->get($userId);
        if ($userData['ruleIds']){
            $ruleIds = unserialize($userData['ruleIds']);
            $ruleIds && $ids = array_merge($ids, $ruleIds);
        }
        //去重
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$userId . $t] = [];
            return [];
        }
        $ruleWhere = '';
        $ids && $ruleWhere = "`ruleId` in (".implode(',', $ids).")";
        $ruleWhere .= ($ruleWhere ? ' AND ' : '') . " OR `isPublic`=1";
        //读取用户组所有权限规则
        $ruleList = $this->authRuleModel->gets([
            'select' => 'condition,url',
            'where' => $ruleWhere,
        ]);
        //循环规则，判断结果。
        $authList = [];
        foreach ($ruleList as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user    = $this->getUserInfo($userId); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command);//debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = strtolower($rule['url']);
                }
            } else {
                //只要存在就记录
                $authList[] = strtolower($rule['url']);
            }
        }
        $_authList[$userId . $t] = $authList;
        if ($this->_config['authType'] == self::AUTH_TYPE_LOGIN) {
            //规则列表结果保存到session
            $_SESSION['_AUTH_LIST_' . $userId . $t] = $authList;
        }

        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库.
     *
     * @param mixed $userId
     */
    protected function getUserInfo($userId)
    {
        static $userinfo = [];
        if (!isset($userinfo[$userId])) {
            $userinfo[$userId] = $this->userModel->get($userId);
        }

        return $userinfo[$userId];
    }
}
