<?php
/*
 * 小程序api类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class WxCstLinkman extends Action {

	private $cacheDir =''; //缓存目录
	private $linkman ='';
	public function __construct() {
		_instance('Action/Auth');
		$this->linkman=_instance('Action/CstLinkman');
	}
	public function cst_linkman(){
		$assArr = $this->linkman->cst_linkman();
		echo json_encode($assArr);
	}
	public function cst_linkman_show(){
		$assArr  = $this->linkman->cst_linkman();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/cst_linkman_show.html');	
	}
	
	
	
		
}//end class
?>