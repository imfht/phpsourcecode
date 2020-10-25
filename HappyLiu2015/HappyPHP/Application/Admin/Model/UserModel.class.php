<?php

namespace Admin\Model;
use Think\Model;

class UserModel extends Model {
    /**
     * 登录用户名和密码登录
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($username, $password){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->where(array('username'=>$username))->find();
        if(!$user || 1 != $user['status']) {
            $this->error = '用户不存在或已被禁用！';
            return false;
        } else {
            if(md5(C('ENCODE_KEY').$password) != $user['password']) {
                $this->error = '用户密码错误!';
                return false;
            }
        }

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
        session('[destroy]'); // session_destroy();
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['username'],
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }

    /**
     * 根据用户ID获取用户昵称
     *
     * @param $uid
     * @return mixed
     */
    public function getNickName($uid){
        return $this->where(array('uid'=>(int)$uid))->getField('nickname');
    }

}
