<?php

namespace Admin\Model;
use Think\Model;

/**
 * 用户模型
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class MemberModel extends Model {

    /**
     * 数据表前缀
     * @var string
     */
    protected $tablePrefix = "u_";

    /**
     * 数据表
     * @var string
     */
    protected $tableName = "User";


    protected $_validate = array(
        array('nickname', '1,16', '昵称长度为1-16个字符', self::EXISTS_VALIDATE, 'length'),
        array('nickname', '', '昵称被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
    );

    public function lists($status = 1, $order = 'uid DESC', $field = true){
        $map = array('status' => $status);
        return $this->field($field)->where($map)->order($order)->select();
    }

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);

        if(!$user || 1 != $user['user_status']) {
            $this->error = '用户不存在或已被禁用！'; //应用级别禁用
            return false;
        }
        //记录行为
        action_log('user_login', 'member', $uid, $uid);

        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['user_id'],
            'username'        => $user['user_name']
        );
        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }

}
