<?php
namespace app\common\upgrade;

class U1{
	public function up(){
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/admin_menu/edit.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/admin_menu/index.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/alonepage/edit.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/alonepage/index.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/group/add.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/group/index.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/member/index.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/module/index.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/plugin/edit.htm');
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/plugin/index.htm');
	}
}