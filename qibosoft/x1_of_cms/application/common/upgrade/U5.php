<?php
namespace app\common\upgrade;

class U5{
	public function up(){
	    
		 @unlink(ROOT_PATH.'template/member_style/default/shop/content/pc_add.htm');
		 
	}
}