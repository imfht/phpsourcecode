<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace User\Controller;
use Common\Api\UserApi;
/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class PublicController extends \Common\Controller\FrontController {

	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			

			/* 调用注册接口注册用户 */
            $User = new UserApi;
			$uid = $User->register($username, $password, $email);
			if(0 < $uid){ //注册成功
				//TODO: 发送验证邮件
				$Member = D('Member');
				$Member->login($username,$password); //登录用户
				Hook('SyncRegister',$uid);
				session('user',null);
				session('token',null);
				$this->success('注册成功！',U('User/Index/index'));
			} else { //注册失败，显示错误信息
				$this->error($this->showRegError($uid));
			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 调用UC登录接口登录 */
			$user = new UserApi;
			$uid = $user->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$Member = D('Member');
				if($Member->login($uid)){ //登录用户
					//TODO:跳转到登录前页面
					//session('prev_url') ? $this->success('登录成功！',session('preg_url')) : $this->success('登录成功！',U('User/Index/index'));
					if(session('prev_url')){
						$this->success('登录成功！',session('prev_url'));
					}else{
						$this->success('登录成功！',U('User/Index/index'));
					}
				} else {
					$this->error($Member->getError());
				}

			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}

		} else { //显示登录表单
			$this->setSeo("用户登入");
			$this->display();
		}
	}

	/**
	 * 找回密码
	 */
	public function forgetStatus(){
		$model = D('Member');
		if(IS_POST){
			$uid = think_decrypt(I('post.uid'));
			if(empty($uid)){
				$this->error('此验证码已过期！' , U('Home/Index/index'));
			}
			$map = $model->create();
			if(!$map){
				$this->error($this->showRegError($model->getError()));
			}
			$data['password'] = $map['password']; 
			$data['salt'] = $map['salt'];
			$data['uid'] = $uid;
			$status = $model->save($data);
			if($status){
				$this->success('找回密码成功！' , U('User/Public/login'));
			}else{
				$this->error('找回密码失败！' , U('Home/Index/index'));
			}
		}else{
			$uid = I('get.status');
			$status = think_decrypt($uid);
			if(empty($status)){
				$this->error('此验证码已过期！' , U('Home/Index/index'));
			}
			$map = array('uid' => $status);
			$find = $model->where($map)->find();
			if($find){
				$this->assign('uid' , $uid);
				$this->display();
			}else{
				$this->error('找不到此验证码！' , U('Home/Index/index'));
			}
		}
	}

	/**
	 * 忘记密码
	 * @param forget 是否已发送邮件
	 */
	public function forget($forget = true){
		if(IS_POST){
			$model = D('Member');
			$map = array('email' => I('post.email') , 'status' => 1);
			$find = $model->where($map)->field('email,uid')->find();
			if(!$find){
				$this->error('此邮箱未注册或未激活！');
			}
			$url = C('WEB_SITE_URL').U('User/Public/forgetStatus/',array('status' => think_encrypt($find['uid'] , null , 600)));
			$content = '您好！您正在申请找回密码，请点击此<a href="'.$url.'">'.$url.'</a>重置密码，此链接10分钟内有效！';
			$status = sendMail($find['email'] , C('WEB_SITE_TITLE').'-找回密码' , $content);
			if(true == $status){
				$this->assign('forget' , false);
			}else{
				$this->error($status);
			}
		}else{
			$this->assign('forget' , true);
		}
		$this->display();
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			$this->success('退出成功！');
		} else {
			$this->redirect('User/Public/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify(array('length'=>4,'useCurve'=>false));
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}

}