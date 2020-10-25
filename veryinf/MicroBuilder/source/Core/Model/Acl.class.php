<?php
namespace Core\Model;
use Think\Model;

class Acl extends Model {
    protected $autoCheckFields = false;

    const STATUS_DISABLED = '-1';
    const STATUS_ENABLED = '0';

    public function getRoles($withDisabled = false) {
        $ret = array();
        $ret[] = array(
            'id'        => '-1',
            'title'     => '[系统]超级管理员',
            'parent'    => '0',
            'status'    => '0',
            'issystem'  => true,
            'remark'    => '系统默认用户组, 拥有系统所有权限. 只有超级管理员才能访问管理中心, 其他用户只能访问工作台.'
        );
        $ret[] = array(
            'id'        => '0',
            'title'     => '[系统]基本用户',
            'parent'    => '0',
            'status'    => '0',
            'issystem'  => true,
            'remark'    => '系统默认用户组, 拥有系统基本权限. 没有设置访问权限的所有页面'
        );
        $condition = '';
        $pars = array();
        if(!$withDisabled) {
            $condition = '`status`=:status';
            $pars[':status'] = self::STATUS_ENABLED;
        }
        $roles = $this->table('__USR_ROLES__')->where($condition)->bind($pars)->select();
        if(!empty($roles)) {
            $ret = array_merge($ret, $roles);
        }
        return $ret;
    }

    public function removeRole($id) {
        $id = intval($id);
        $ret = $this->table('__USR_ROLES__')->where("`id`={$id}")->delete();
        return !!$ret;
    }

    public function getUser($username, $withDisabled = false) {
        $pars = array();

        if(is_int($username)) {
            $condition = '`uid`=:uid';
            $pars[':uid'] = $username;
        } else {
            $condition = '`username`=:username';
            $pars[':username'] = $username;
        }

        if(!$withDisabled) {
            $condition .= ' AND `status`=:status';
            $pars[':status'] = self::STATUS_ENABLED;
        }
        $user = $this->table('__USR_USERS__')->where($condition)->bind($pars)->find();
        return $user;
    }
    
    public function createUser($user) {
        $user = coll_elements(array('username', 'password', 'role'), $user);
        $exist = $this->getUser($user['username'], true);
        if(!empty($exist)) {
            return error(-1, '用户名已经存在, 请返回修改');
        }
        $user['salt'] = util_random(8);
        $user['status'] = self::STATUS_ENABLED;
        $user['password'] = Utility::encodePassword($user['password'], $user['salt']);

        $ret = $this->table('__USR_USERS__')->data($user)->add();
        if(!empty($ret)) {
            return $this->getLastInsID();
        }
        return error(-2, '保存用户数据失败, 请稍后重试');
    }
    
    public function modifyUser($uid, $user) {
        $uid = intval($uid);
        $input = coll_elements(array('password', 'role', 'status'), $user);
        $user = $this->getUser($uid);
        $input['password'] = Utility::encodePassword($input['password'], $user['salt']);
        $ret = $this->table('__USR_USERS__')->data($input)->where("`uid`={$uid}")->save();
        if($ret !== false) {
            return true;
        }
        return error(-2, '保存用户数据失败, 请稍后重试');
    }
    
    public function removeUser($uid) {
        $uid = intval($uid);
        if($uid == '1') {
            return error(-1, '创建用户不能删除');
        }
        $user = $this->getUser($uid, true);
        if(empty($user)) {
            return error(-2, '访问错误');
        }
        $ret = $this->table('__USR_USERS__')->where("`uid`={$uid}")->delete();
        if(empty($ret)) {
            return error(-3, '删除用户信息失败, 请稍后重试');
        } else {
            return true;
        }
    }
}
