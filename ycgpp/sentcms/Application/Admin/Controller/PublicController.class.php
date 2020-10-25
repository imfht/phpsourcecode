<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Common\Api\UserApi;

class PublicController extends \Common\Controller\FrontController {
	/**
	* 后台用户登录
	* @author 麦当苗儿 <zuojiazi@vip.qq.com>
	*/
	public function login($username = null, $password = null, $verify = null){
		if(IS_POST){
			// /* 检测验证码 TODO: */
			// if(!check_verify($verify)){
			// 	$this->error('验证码输入错误！');
			// }

			/* 调用UC登录接口登录 */
			$User = new UserApi;
			$uid = $User->login($username, $password);
			if(0 < $uid){ //UC登录成功
				/* 登录用户 */
				$this->success('登录成功！', U('Index/index'));
			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}
		} else {
			if(is_login()){
				$this->redirect('Index/index');
			}else{
				$this->display();
			}
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			session('[destroy]');
			$this->success('退出成功！', U('login'));
		} else {
			$this->redirect('login');
		}
	}

	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}
}