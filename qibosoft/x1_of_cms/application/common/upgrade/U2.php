<?php
namespace app\common\upgrade;

class U2{
	public function up(){
		 unlink(ROOT_PATH.'/template/admin_style/default/admin/module/copy.htm');
	}
}