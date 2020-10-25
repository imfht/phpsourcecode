<?php

/**
 * 会员注册登录
 */

namespace Admin\Controller;

use Common\Controller\AdminbaseController;

class MemberController extends AdminbaseController {

	function index() {
		$lists = M("Members")->where("user_status=1")->select();
		$this->assign('lists', $lists);
		$this->display();
	}

	function delete() {
		$id = intval($_GET['id']);
		if ($id) {
			$rst = M("Members")->where("user_status=1 and ID=$id")->setField('user_status', '0');
			if ($rst) {
				$this->success("保存成功！", U("admin/member/index"));
			} else {
				$this->error('会员删除失败！');
			}
		} else {
			$this->error('数据传入失败！');
		}
	}

}
