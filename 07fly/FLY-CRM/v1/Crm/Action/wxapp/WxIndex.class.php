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
class WxIndex extends Action {

	private $cacheDir =''; //缓存目录
	private $menulist ='';
	public function __construct() {
		_instance('Action/Auth');
	}
	
	public function index(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/index.html');	
	}
	
	public function crm(){
		$smarty  = $this->setSmarty();
/*		$smarty->assign($assArr);*/
		$smarty->display('wxapp/crm.html');	
	}
	public function cus(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/cus.html');	
	}
	
//客户信息调用接口
	public function customer(){
		$assArr = $this->L('Customer')->customer();
		echo json_encode($assArr);
	}
	public function customer_show(){
		$assArr = $this->L('Customer')->customer();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/customer_show.html');	
	}
	
	
	

	public function linkman(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/linkman.html');	
	}
	public function cst_trace_show(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/cst_trace_show.html');	
	}
	public function sal_contract_show(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/sal_contract_show.html');	
	}
	public function sal_order_show(){
		$smarty  = $this->setSmarty();
		$smarty->display('wxapp/sal_order_show.html');	
	}
	
	
	
}//end class
?>