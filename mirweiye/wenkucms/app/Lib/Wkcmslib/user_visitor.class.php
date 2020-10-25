<?php
/**
 * 访问者
 *
 * @author andery
 */
class user_visitor {

    public $is_login = false; //登陆状态
    public $info = null;

    public function __construct() {
    	
    	//dump($_COOKIE);
    	//dump($_SESSION);
    	
        if (session('?user_info')) {
            //已经登陆
            $this->info = session('user_info');
            $this->is_login = true;
        } elseif ($user_info = (array)cookie('user_info')) {
            $user_info = M('user')->field('uid,username')->where(array('uid'=>$user_info['uid'], 'password'=>$user_info['password']))->find();
            if ($user_info) {
                //记住登陆状态
                $this->assign_info($user_info);
                 
                $this->is_login = true;
            }
        } else {
            $this->is_login = false;
        }
    }

    /**
     * 登陆会话
     */
    public function assign_info($user_info) {
        session('user_info', $user_info);
        $this->info = $user_info;
    }

    /**
     * 记住密码
     */
    public function remember($user_info, $remember = null) {
        if ($remember) {
            $time = 3600 * 24 * 14; //两周
            cookie('user_info', array('uid'=>$user_info['uid'], 'password'=>$user_info['password']), $time);
        }
    }

    /**
     * 获取用户信息
     */
    public function get($key = null) {
        $info = null;
        if (is_null($key) && $this->info['uid']) {
            $info = M('user')->find($this->info['uid']);
        } else {
            if (isset($this->info[$key])) {
                return $this->info[$key];
            } else {
                //获取用户表字段
                $fields = M('user')->getDbFields();
                if (!is_null(array_search($key, $fields))) {
                    $info = M('user')->where(array('uid' => $this->info['uid']))->getField($key);
                }
            }
        }
        return $info;
    }

    /**
     * 登陆
     */
    public function login($uid, $remember = null) {
        $user_mod = M('user');
        //更新用户信息
       
        $user_info = $user_mod->field('uid,username,password')->find($uid);
        //保持状态
        $this->assign_info($user_info);
        $this->remember($user_info, $remember);
    }

    /**
     * 退出
     */
    public function logout() {
        session('user_info', null);
        cookie('user_info', null);
    }

}