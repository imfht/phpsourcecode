<?php
use Admin\Model\AuthRuleModel;
/**
 * 用户相关公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}
/**
 * 通用需要用户授权判断 登陆状态返回用户uid，未登陆返回false，如果安装了微信模块可调起微信网页授权
 * @return [type] [description]
 */
function _need_login(){
    return D('Common/Member')->need_login();
}
/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0)
{
    return D('Common/Member')->get_username($uid);
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0)
{
    return D('Common/Member')->get_nickname($uid);
}
function UCenterMember()
{
    return D('User/UcenterMember');
}
/**
 * 用户扩展资料可添加关联字段
 * @param string $id 关联数据表ID
 * @param string $field 需要返回的字段内容
 * @param string $table 关联数据表
 * @return array string
 * @author MingYang <xint5288@126.com>
 */
function get_userdata_join($id = null, $field = null, $table = null)
{
    if (empty($table) || empty($field)) {
        return false;
    }
    if (empty($id)) {
        $data = D($table)->select();
        foreach ($data as $key => $val) {
            $list[$key] = $val;
        }
        return $list;
    } else {
        if (is_array($id)) {
            $map['id'] = array('in', $id);
            $data = D($table)->where($map)->getField($field, true);
            return implode(',', $data);
        } else {
            $map['id'] = $id;
            $data = D($table)->where($map)->getField($field);
            return $data;
        }
    }
}

/**
 * 构造用户配置表 D('UserConfig')查询条件
 * @param string $name 表中name字段的值(配置标识)
 * @param string $model 表中model字段的值(模块标识)
 * @param int $uid 用户uid
 * @param int $role_id 登录的角色id
 * @return array 查询条件 $map
 * @author 郑钟良<zzl@ourstu.com>
 */
function getUserConfigMap($name = '', $model = '', $uid = 0, $role_id = 0)
{
    $uid = $uid ? $uid : is_login();
    $role_id = $role_id ? $role_id : get_role_id($uid);
    $map = array();
    //构造查询条件
    $map['uid'] = $uid;
    $map['name'] = $name;
    $map['role_id'] = $role_id;
    $map['model'] = $model;
    return $map;
}

function get_uid()
{
    return is_login();
}

/**
 * 检测权限
 */
function CheckPermission($uids)
{
    if (is_administrator()) {
        return true;
    }
    if (in_array(is_login(), $uids)) {
        return true;
    }
    return false;
}

function check_auth($rule = '', $except_uid = -1, $type = AuthRuleModel::RULE_URL)
{
    if (is_administrator()) {
        return true;//管理员允许访问任何页面
    }
    if ($except_uid != -1) {
        if (!is_array($except_uid)) {
            $except_uid = explode(',', $except_uid);
        }
        if (in_array(is_login(), $except_uid)) {
            return true;
        }
    }
    $rule = empty($rule) ? MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME : $rule;
    // 检测是否有该权限
    if (!M('auth_rule')->where(array('name' => $rule, 'status' => 1))->find()) {
        return false;
    }
   static $Auth = null;
    if (!$Auth) {
        $Auth = new \Think\Auth();
    }
    if (!$Auth->check($rule, get_uid(), $type)) {
        return false;
    }
    return true;
}


/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    $admin_uids = explode(',', C('USER_ADMINISTRATOR'));//调整验证机制，支持多管理员，用,分隔
    //dump($admin_uids);exit;
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}
function get_administrator()
{
    $admin_uids = explode(',', C('USER_ADMINISTRATOR')); //调整验证机制，支持多管理员，用,分隔
    return $admin_uids;
}

/**获得具有某个权限节点的全部用户UID数组
 * @param string $rule
 */
function get_auth_user($rule = '')
{
    $rule = D('AuthRule')->where(array('name' => $rule))->find();
    $groups = D('AuthGroup')->select();
    $uids = array();
    foreach ($groups as $v) {
        $auth_rule = explode(',', $v['rules']);
        if (in_array($rule['id'], $auth_rule)) {
            $gid = $v['id'];
            $temp_uids =(array) D('AuthGroupAccess')->where(array('group_id' => $gid))->getField('uid');
            if ($temp_uids !== null) {
                $uids = array_merge($uids, $temp_uids);
            }
        }
    }
    $uids = array_merge($uids, get_administrator());
    $uids = array_unique($uids);
    return $uids;
}
/**
 * check_username  根据type或用户名来判断注册使用的是用户名、邮箱或者手机
 * @param $username
 * @param $email
 * @param $mobile
 * @param int $type
 * @return bool
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function check_username(&$username, &$email, &$mobile, &$type = 0)
{

    if ($type) {
        switch ($type) {
            case 2:
                $email = $username;
                $username = '';
                $mobile = '';
                $type = 2;
                break;
            case 3:
                $mobile = $username;
                $username = '';
                $email = '';
                $type = 3;
                break;
            default :
                $mobile = '';
                $email = '';
                $type = 1;
                break;
        }
    } else {
        $check_email = preg_match("/[a-z0-9_\-\.]+@([a-z0-9_\-]+?\.)+[a-z]{2,3}/i", $username, $match_email);
        $check_mobile = preg_match("/^(1[0-9])[0-9]{9}$/", $username, $match_mobile);
        if ($check_email) {
            $email = $username;
            $username = '';
            $mobile = '';
            $type = 2;
        } elseif ($check_mobile) {
            $mobile = $username;
            $username = '';
            $email = '';
            $type = 3;
        } else {
            $mobile = '';
            $email = '';
            $type = 1;
        }
    }
    return true;
}

/**
 * check_reg_type  验证注册格式是否开启
 * @param $type
 * @return bool
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function check_reg_type($type){
    $t[1] = $t['username'] ='username';
    $t[2] = $t['email'] ='email';
    $t[3] = $t['mobile'] ='mobile';

    $switch = modC('REG_SWITCH','','USERCONFIG');
    if($switch){
        $switch = explode(',',$switch);
        if(in_array($t[$type],$switch)){
           return true;
        }
    }
    return false;

}


/**
 * check_login_type  验证登录提示信息是否开启
 * @param $type
 * @return bool
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function check_login_type($type){
    $t[1] = $t['username'] ='username';
    $t[2] = $t['email'] ='email';
    $t[3] = $t['mobile'] ='mobile';

    $switch = modC('LOGIN_SWITCH','username','USERCONFIG');
    if($switch){
        $switch = explode(',',$switch);
        if(in_array($t[$type],$switch)){
            return true;
        }
    }
    return false;

}

/**
 * get_next_step  获取注册流程下一步
 * @param string $now_step
 * @return string
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function get_next_step($now_step =''){

    $step = get_kanban_config('REG_STEP', 'enable','', 'USERCONFIG');
    if(empty($now_step) || $now_step == 'start'){
        $return = $step[0];
    }else{
        $now_key = array_search($now_step,$step);
        $return = $step[$now_key+1];
    }
    if(!in_array($return,array_keys(A('Ucenter/RegStep','Widget')->mStep)) || empty($return)){
        $return = 'finish';
    }
    return $return;
}

/**
 * check_step
 * @param string $now_step
 * @return string
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function check_step($now_step=''){
    $step = get_kanban_config('REG_STEP', 'enable','', 'USERCONFIG');
    if(array_search($now_step,$step)){
        $return = $now_step;
    }
    else{
        $return = $step[0];
    }
    return $return;
}


/**
 * set_user_status   设置用户状态
 * @param $uid
 * @param $status
 * @return bool
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function set_user_status($uid,$status){
    D('Member')->where(array('uid'=>$uid))->setField('status',$status);
    UCenterMember()->where(array('id'=>$uid))->setField('status',$status);
    return true;
}

/**
 * set_users_status   批量设置用户状态
 * @param $map
 * @param $status
 * @return bool
 * @author 郑钟良<zzl@ourstu.com>
 */
function set_users_status($map,$status){
    D('Member')->where($map)->setField('status',$status);
    UCenterMember()->where($map)->setField('status',$status);
    return true;
}

/**
 * check_step_can_skip  判断注册步骤是否可跳过
 * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
 */
function check_step_can_skip($step){
    $skip = modC('REG_CAN_SKIP','', 'USERCONFIG');
    $skip = explode(',',$skip);
    if(in_array($step,$skip)){
        return true;
    }
    return false;
}



function check_and_add($args){
    $Member = D('Member');
    $uid = $args['uid'];

    $check = $Member->find($uid);
    if(!$check){
        $args['status'] =1;
        $Member-> add($args);
    }
    return true;
}

/**
 * @param $content
 * @return mixed
 */
function match_users($content)
{
    $user_pattern = "/\@([^\#|\s]+)\s/"; //匹配用户
    preg_match_all($user_pattern, $content, $user_math);
    return $user_math;
}