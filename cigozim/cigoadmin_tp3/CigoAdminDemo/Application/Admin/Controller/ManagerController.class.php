<?php

namespace Admin\Controller;

use Admin\Lib\AdminUser;
use CigoAdminLib\Lib\Admin;
use CigoAdminLib\Lib\SessionCheck;

class ManagerController extends SessionCheck
{
	public function modifyNickName()
	{
		if (IS_POST) {
			$userApi = new AdminUser();
			$result = $userApi->modifyNickName($_POST);

			if (!$result[Admin::DATA_TAG_RES]) {
				$this->error($result[Admin::DATA_TAG_INFO]);
			} else {
				$this->success($result[Admin::DATA_TAG_INFO], U('Index/index'));
			}
		} else {
			$this->assign('label_title', '修改昵称');
			$this->display();
		}
	}

	public function modifyPwd()
	{
		if (IS_POST) {
			//TODO 演示关闭

//			$userApi = new AdminUser();
//			$result = $userApi->modifyPwd($_POST);
//
//			if (!$result[Admin::DATA_TAG_RES]) {
//				$this->error($result[Admin::DATA_TAG_INFO]);
//			} else {
//				$this->success($result[Admin::DATA_TAG_INFO], U('Index/index'));
//			}

			$this->success('亲，你想干嘛啊！', U('Index/index'));
		} else {
			$this->assign('label_title', '修改密码');
			$this->display();
		}
	}
}
