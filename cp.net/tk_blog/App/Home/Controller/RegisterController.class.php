<?php
/**
 * 会员注册
 */
namespace Home\Controller;
use Common\Controller\HomebaseController;
use Common\Model\UsersModel;
class RegisterController extends HomebaseController {
	/**
	 * AJAX 登录操作
	 */
	public function do_login(){
		if (IS_AJAX) {
			$UserModel = new UsersModel();
			$url = __ROOT__."/";
			if ($UserModel->do_login()) {
				exit(json_encode(array('status'=>1,'url'=>$url,'msg'=>'登录成功,马上为您跳转..^_^')));
			} else {
				//验证失败
				exit(json_encode(array('status'=>0,'url'=>'','msg'=>$UserModel->getError())));
			}
		}
	}

	/**
	 * AJAX 注册操作
	 */
	public function do_register(){
		if (IS_AJAX) {
			$UserModel = new UsersModel();
			$url = __ROOT__."/";
			if ($result = $UserModel->do_Users()) {
				//验证成功 发送短信
				$_SESSION['user']=$result;
				//发送密码到邮箱
				$error = $this->sendEmaill_to_User($result['password']);
				if ($error) {
					//邮箱发送失败 删除数据
					$UserModel->where(array('uid'=>$result['uid']))->delete();
					exit(json_encode(array('status'=>0,'url'=>'','msg'=>$error)));
				} else {
					unset($_SESSION['user']);
					exit(json_encode(array('status'=>1,'url'=>$url,'msg'=>'注册成功,密码已发送到您的邮箱.^_^')));
				}
			} else {
				//验证失败
				exit(json_encode(array('status'=>0,'url'=>'','msg'=>$UserModel->getError())));
			}
		}

	}

	/**
	 * 调用验证码
	 */
	public function verifycode(){
		$config =    array(
			'fontSize'    =>    23,    // 验证码字体大小
			'length'      =>    4,     // 验证码位数
			'useNoise'    =>    false, // 关闭验证码杂点
		);
		$Verify =     new \Think\Verify($config);
		$Verify->entry();
	}
}