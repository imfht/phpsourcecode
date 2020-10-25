<?php

namespace Admin\Controller;

use Admin\Lib\AdminUser;
use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Logic\UserCtrlLogic;
use Think\Verify;

class LoginController extends Admin {
	public function index() {
		$this->assign('pageTitle', 'CigoAmin测试后台');
		$this->display();
	}

	public function doLogIn() {
		if (!IS_POST) {
			$this->error('访问异常!');
		}

		/* 检测用户名是否为空 */
		if (empty($_POST['username'])) {
			$this->error('请输入用户名！');
		}
		/* 检测密码是否为空 */
		if (empty($_POST['password'])) {
			$this->error('请输入密码！');
		}
		/* 检测验证码 TODO: */
		if (empty($_POST['verify_code'])) {
			$this->error('请输入验证码！');
		}
		if (!check_verify($_POST['verify_code'], Admin::VERIFY_CODE_TYPE_ADMIN)) {
			$this->error('验证码输入错误！');
		}

		/* 调用UC登录接口登录 */
		$userApi = new AdminUser();
		$result = $userApi->doLogIn(array(
			UserCtrlLogic::DATA_TAG_USERNAME => $_POST['username'],
			UserCtrlLogic::DATA_TAG_PASSWORD => $_POST['password']
		));

		//登陆成功
		if ($result[Admin::DATA_TAG_RES]) {
			$this->success($result[Admin::DATA_TAG_INFO], U('Index/index'));
		} else { // 登录失败
			$this->error($result['info']);
		}
	}

	public function verifyCode() {
		$verify = new Verify(
			array(
				'useCurve' => false,
				'length' => 4,
				'fontSize' => 30
			)
		);
		$verify->entry(Admin::VERIFY_CODE_TYPE_ADMIN);
	}
}
