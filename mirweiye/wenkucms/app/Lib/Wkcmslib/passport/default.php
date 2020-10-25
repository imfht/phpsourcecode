<?php
/**
 * 内置用户中心连接接口
 * author: andery@foxmail.com
 */
class default_passport
{

    private $_error = 0;

    public function __construct() {
        $this->_user_mod = D('user');
    }

    public function get_info() {
        return array(
            'code' => 'default', //插件代码必须和文件名保持一致
            'name' => 'WKCMS', //整合插件名称
            'desc' => 'WKCMS 默认会员系统',
            'version' => '1.0', //整合插件的版本
            'author' => 'WKCMS', //开发者
        );
    }

    /**
     * 注册新用户
     */
    public function register($username, $password, $email) {
        if (!$this->check_username($username)) {
            $this->_error = L('username_exists');
            return false;
        }
        if (!$this->check_email($email)) {
            $this->_error = L('email_exists');
            return false;
        }
        
        return array(
            'username' => $username,
            'password' => $password,
            'email' => $email
            
        );
    }
   public function uc_avatar($uid){
    	
    	
    	$html=1;
    	return $html;
    	
    }
    /**
     * 修改用户资料
     */
    public function edit($uid, $old_password, $data, $force = false) {
        if (!$force) {
            //先验证
            $info = $this->get($uid);
            if ($info['password'] != md5($old_password.C('WEB_MD5'))) {
                $this->_error = L('auth_failed');
                return false;
            }
        }
        if (isset($data['password'])) {
            $data['password'] = $data['password'].C('WEB_MD5');
        }
        return $data;
    }

    /**
     * 删除用户
     */
    public function delete() {
        return true;
    }

    public function get($flag, $is_name = false) {
        if ($is_name) {
            $map = array('username' => $flag);
        } else {
            $map = array('uid' => intval($flag));
        }
        return M('user')->where($map)->find();
    }

    /**
     * 登陆验证
     */
    public function auth($username, $password) {
        $uid = M('user')->where(array('username'=>$username,'status'=>1, 'password'=>md5($password.C('WEB_MD5'))))->getField('uid');
        if ($uid) {
            return $uid;
        } else {
            $this->_error = L('auth_failed');
            return false;
        }
    }

    /**
     * 同步登陆
     */
    public function synlogin() {}

    /**
     * 同步退出
     */
    public function synlogout() {}

    /**
     * 检测用户邮箱唯一
     */
    public function check_email() {
        if ($this->_user_mod->where(array('email'=>$email))->count('uid')) {
            return false;
        }
        return true;
    }

    /**
     * 检测用户名唯一
     */
    public function check_username() {
        if ($this->_user_mod->where(array('username'=>$username))->count('uid')) {
            return false;
        }
        return true;
    }

    public function get_error() {
        return $this->_error;
    }
}