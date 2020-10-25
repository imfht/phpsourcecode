<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------

namespace User\Api;
use User\Api\Api;
use User\Model\UcenterUserModel;

class UserApi extends Api{
    /**
     * 构造方法，实例化操作模型
     */
    protected function _init(){
        $this->model = new UcenterUserModel();
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $password, $email, $mobile = ''){
        return $this->model->register($username, $password, $email, $mobile);
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 2){
        return $this->model->login($username, $password, $type);
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_doname = false){
        return $this->model->info($uid, $is_doname);
    }

    /**
     * 检测用户名
     * @param  string  $field  用户名
     * @return integer         错误编号
     */
    public function checkUsername($username){
        return $this->model->checkField($username, 1);
    }

    /**
     * 检测邮箱
     * @param  string  $email  邮箱
     * @return integer         错误编号
     */
    public function checkEmail($email){
        return $this->model->checkField($email, 2);
    }

    /**
     * 检测手机
     * @param  string  $mobile  手机
     * @return integer         错误编号
     */
    public function checkMobile($mobile){
        return $this->model->checkField($mobile, 3);
    }
    /**
     * 检测个性域名 小麦新增
     * @param  string  $doname  个性域名
     * @return integer         错误编号
     */
    public function checkDoname($doname){
        return $this->model->checkField($doname, 4);
    }    

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author 小麦 <810578553@qq.com>
     */
    public function updateInfo($uid, $data){
        if($this->model->updateUserFields($uid, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }
    /**
     * 更新用户密码
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author 小麦 <810578553@qq.com>
     */
    public function updatePassword($uid, $password, $data){
        if($this->model->updatePassWord($uid, $password, $data) !== false){
            $return['status'] = true;
        }else{
            $return['status'] = false;
            $return['info'] = $this->model->getError();
        }
        return $return;
    }   
	/**
	 * 验证email是否存在
	 * @param int $uid 用户id
	 * @param string $email 密码
	 * @return true 验证成功，false 验证失败
	 * @author 小麦 <810578553@qq.com>
	 */	
	public function email_exists($email, $id = 0) {
		return $this->model->email_exists($email,$id);
	}
	/**
	 * 验证username是否存在
	 * @param int $uid 用户id
	 * @param string $email 密码
	 * @return true 验证成功，false 验证失败
	 * @author 小麦 <810578553@qq.com>
	 */		
	public function username_exists($name, $id = 0) {
		return $this->model->username_exists($name,$id);
	}
	/**
	 * 验证doname是否存在
	 * @param int $uid 用户id
	 * @param string $email 密码
	 * @return true 验证成功，false 验证失败
	 * @author 小麦 <810578553@qq.com>
	 */		
	public function doname_exists($doname, $id = 0)
	{
		return $this->model->username_exists($doname,$id);
	}    

}
