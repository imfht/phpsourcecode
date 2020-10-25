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

	private $cacheDir  =''; //缓存目录
	private $pcCustomer ='';
	private $WxCstDict	='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcCustomer=_instance('Action/Customer');
		$this->WxCstDict =_instance('Action/wxapp/WxCstDict');
	}
	public function customer(){
		$assArr = $this->pcCustomer->customer();
		echo json_encode($assArr);
	}
	public function customer_show(){
		$assArr = $this->pcCustomer->customer();
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/customer_show.html');	
	}
	public function customer_show_one(){
		$cusID	= $this->_REQUEST('cusID');
		$one   = $this->pcCustomer->customer_get_one($cusID);
		$assArr	= array("one"=>$one);
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/customer_show_one.html');	
	}
	public function customer_add(){
		if(empty($_POST)){
			$source		= $this->WxCstDict->cst_dict_opt('source','source_id');
			$level  	= $this->WxCstDict->cst_dict_opt('level','level_id');
			$ecotype 	= $this->WxCstDict->cst_dict_opt('ecotype','ecotype_id');
			$trade 		= $this->WxCstDict->cst_dict_opt('trade','trade_id');
			$assArr	= array("source"=>$source,"level"=>$level,"ecotype"=>$ecotype,"trade"=>$trade);
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/customer_add.html');
		}else{
			$rtn=$this->pcCustomer->customer_add_save();
			$assArr	= array("msg"=>"操作成功","code"=>200,"gotourl"=>"wxapp/WxCustomer/customer_show/");
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/alter.html');
		}
	}
	public function alter(){
		$assArr	= array("msg"=>"操作成功","code"=>200,"gotourl"=>"wxapp/WxCustomer/customer_show/");
		$smarty = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('wxapp/alter.html');
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