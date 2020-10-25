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
class WxCustomer extends Action {

	private $cacheDir =''; //缓存目录
	private $customer ='';
	public function __construct() {
		_instance('Action/Auth');
		$this->customer=_instance('Action/Customer');
	}
	public function customer(){
		$assArr = $this->customer->customer();
		echo json_encode($assArr);
	}
	public function customer_show(){
		$assArr = $this->customer->customer();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/customer_show.html');	
	}
	public function customer_show_one(){
		$cusID	= $this->_REQUEST('cusID');
		$one   = $this->customer->customer_get_one($cusID);
		$assArr	= array("one"=>$one);
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/customer_show_one.html');	
	}
	public function customer_get_one($id=""){
		if($id){
		  $sql = "select * from cst_customer where id='$id'";
		  $one = $this->C($this->cacheDir)->findOne($sql);	
		  return $one;
		}
	}
		
}//end class
?>