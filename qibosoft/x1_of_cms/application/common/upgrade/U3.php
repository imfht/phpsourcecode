<?php
namespace app\common\upgrade;

class U3{
	public function up(){
		 unlink(ROOT_PATH.'template/admin_style/default/plugins/weixin/weixin_autoreply/index.htm');
	}
}