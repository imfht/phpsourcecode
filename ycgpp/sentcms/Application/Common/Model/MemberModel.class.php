<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;
use User\Api\UserApi;

/**
 * 文档基础模型
 */
class MemberModel extends Model{

    protected $salt;

    /* 用户模型自动验证 */
    protected $_validate = array(
        /* 验证用户名 */
        array('username', '1,30', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
        array('username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback',self::MODEL_INSERT), //用户名禁止注册
        array('username', '', -3, self::EXISTS_VALIDATE, 'unique' ,self::MODEL_INSERT), //用户名被占用

        /* 验证密码 */
        array('password', '6,30', -4, self::EXISTS_VALIDATE, 'length',self::MODEL_INSERT), //密码长度不合法
        array('password', '6,30', -4, self::VALUE_VALIDATE, 'length',self::MODEL_UPDATE), //不为空的时候验证

        /* 验证邮箱 */
        array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
        array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
        array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
        array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用

        /* 验证手机号码 */
        array('mobile', '//', -9, self::EXISTS_VALIDATE), //手机格式不正确 TODO:
        array('mobile', 'checkDenyMobile', -10, self::EXISTS_VALIDATE, 'callback'), //手机禁止注册
        array('mobile', '', -11, self::EXISTS_VALIDATE, 'unique'), //手机号被占用
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('salt','getSalt', self::MODEL_BOTH, 'callback'),
        array('password', 'getPassword', self::MODEL_BOTH, 'callback'),
        array('login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 0, self::MODEL_INSERT),
        array('last_login_time', 0, self::MODEL_INSERT),
        array('update_time', NOW_TIME),
        array('status', 1, self::MODEL_INSERT),
        array('birthday' , 'formatBirthday' , self::MODEL_BOTH , 'callback'),
    );

    protected function getSalt(){
        $this->salt = rand_string();
        return $this->salt;
    }

    protected function getPassword($password){
        return md5($password.$this->salt);
    }

    protected function formatBirthday($birthday){
        //return strtotime($birthday);
        if(empty($birthday)){
            return '0000-00-00';
        }
        return $birthday;
    }

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($username){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile){
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 根据配置指定用户状态
     * @return integer 用户状态
     */
    protected function getStatus(){
        return true; //TODO: 暂不限制，下一个版本完善
    }


    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $password, $email, $mobile){
        $data = array(
            'username' => $username,
            'password' => $password,
            'email'    => $email,
            'mobile'   => $mobile,
        );

        //验证手机
        if(empty($data['mobile'])) unset($data['mobile']);

        /* 添加用户 */
        if($this->create($data)){
            $uid = $this->add();
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError(); //错误详情见自动验证注释
        }
    }


    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1){
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['uid'] = $username;
                break;
            case 5:
                $map['uid'] = $username;
                break;
            default:
                return 0; //参数错误
        }

        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if(is_array($user) && $user['status']){
            if ($type == '5') {
                $this->autoLogin($user); //更新用户登录信息
                return $user['uid']; //登录成功，返回用户ID
            }
            /* 验证用户密码 */
            if(md5($password.$user['salt']) === $user['password']){
                $this->autoLogin($user); //更新用户登录信息
                return $user['uid']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
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
            'username'        => get_username($user['uid']),
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
    }

    /**
     * 检测用户信息
     * @param  string  $field  用户名
     * @param  integer $type   用户名类型 1-用户名，2-用户邮箱，3-用户电话
     * @return integer         错误编号
     */
    public function checkField($field, $type = 1){
        $data = array();
        switch ($type) {
            case 1:
                $data['username'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['mobile'] = $field;
                break;
            default:
                return 0; //参数错误
        }

        return $this->create($data) ? 1 : $this->getError();
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateUserFields($uid, $password, $data){
        if(empty($uid) || empty($password) || empty($data)){
            $this->error = '参数错误！';
            return false;
        }

        //更新前检查用户密码
        if(!$this->verifyUser($uid, $password)){
            $this->error = '验证出错：密码不正确！';
            return false;
        }

        //更新用户信息
        $data = $this->create($data);
        if($data){
            return $this->where(array('uid'=>$uid))->save($data);
        }
        return false;
    }

    /**
     * 验证用户密码
     * @param int $uid 用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     * @author huajie <banhuajie@163.com>
     */
    protected function verifyUser($uid, $password_in){
        $user = $this->field('password,salt')->where(array('uid'=>$uid))->find();
        if(md5($password_in.$user['salt']) === $user['password']){
            return true;
        }
        return false;
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false){
        $map = array();
        if($is_username){ //通过用户名获取
            $map['username'] = $uid;
        } else {
            $map['uid'] = $uid;
        }

        $user = $this->where($map)->field('uid,username,email,mobile,status')->find();
        if(is_array($user) && $user['status'] = 1){
            return array($user['uid'], $user['username'], $user['email'], $user['mobile']);
        } else {
            return -1; //用户不存在或被禁用
        }
    }
}
