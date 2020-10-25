<?php
namespace app\common\controller;

use app\common\controller\Base;
use think\Controller;

// 会员中心 基础控制器

class BaseMember extends Base {
    public function initialize() {
    	parent::initialize();
		$is_login = $this->restLogin();
		if( !empty($is_login) ){
			$this->error( $is_login ,'index/index/index');
		}

	}

}
