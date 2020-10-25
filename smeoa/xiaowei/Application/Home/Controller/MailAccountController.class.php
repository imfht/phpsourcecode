<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class MailAccountController extends HomeController {
	protected $config = array('app_type' => 'personal');
	public function index() {
		$mail_user = M("MailAccount") -> find(get_user_id());
		$this -> assign('mail_user', $mail_user);
		if (count($mail_user)) {
			$this -> assign('opmode', 'edit');
		} else {
			$this -> assign('opmode', 'add');
		}
		$this -> display();
	}

	protected function _set_email($email) {		
		$data['id'] = get_user_id();
		$data['email'] = $email;
		M("User") -> save($data);
	}

	protected function _insert($name = CONTROLLER_NAME) {
		$model = D($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		if (in_array('id', $model -> getDbFields())) {
			$model -> id = get_user_id();
		};
		if (in_array('user_name', $model -> getDbFields())) {
			$model -> user_name = get_user_name();
		};
		$email = $model -> email;
		//保存当前数据对象
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> _set_email($email);
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('新增成功!');
		} else {
			//失败提示
			$this -> error('新增失败!');
		}
	}

	protected function _update($name = CONTROLLER_NAME) {
		$model = M($name);
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		if (in_array('id', $model -> getDbFields())) {
			$model -> id = get_user_id();
		};
		// 更新数据
		$email = $model -> email;
		$list = $model -> save();
		if (false !== $list) {
			//成功提示
			$this -> _set_email($email);
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
		} else {
			//错误提示
			$this -> error('编辑失败!');
		}
	}

}
?>