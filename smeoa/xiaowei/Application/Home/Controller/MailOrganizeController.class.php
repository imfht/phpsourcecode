<?php
/*--------------------------------------------------------------------
 小微OA系统 - 让工作更轻松快乐

 Copyright (c) 2013 http://www.smeoa.com All rights reserved.

 Author:  jinzhu.yin<smeoa@qq.com>

 Support: https://git.oschina.net/smeoa/xiaowei
--------------------------------------------------------------*/

namespace Home\Controller;

class MailOrganizeController extends HomeController {
	protected $config = array('app_type' => 'personal');
	public function index() {
		$where["user_id"] = get_user_id();
		$list = M("MailOrganize") -> where($where) -> select();
		$this -> assign("list", $list);
		$this -> display();
	}

	function add() {		
		$this -> assign('mail_folder',R("Mail/_assign_mail_folder_list"));
		$this -> display();
	}

	function edit($id) {
		$this -> assign('mail_folder',R("Mail/_assign_mail_folder_list"));
		$this -> assign('folder_list', $mail_folder);
		$this -> _edit($id);
	}

	protected function _update($name = CONTROLLER_NAME) {
		$id = I("id");
		$model = D($name);
		$model -> where("id=$id") -> delete();
					
		if (false === $model -> create()) {
			$this -> error($model -> getError());
		}
		//保存当前数据对象
		$model->user_id=get_user_id();
		$list = $model -> add();
		if ($list !== false) {//保存成功
			$this -> assign('jumpUrl', get_return_url());
			$this -> success('编辑成功!');
		} else {
			//失败提示
			$this -> error('编辑失败!');
		}
	}

	function del($id) {		
		$this -> _destory($id);
	}
}
?>