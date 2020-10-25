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
class WxCstChance extends Action {

	private $cacheDir =''; //缓存目录
	private $pcCstChance  ='';
	private $WxCstDict	='';
	private $WxCstLinkman	='';
	public function __construct() {
		_instance('Action/Auth');
		$this->pcCstChance =_instance('Action/CstChance');
		$this->WxCstDict  =_instance('Action/wxapp/WxCstDict');
		$this->WxCstLinkman  =_instance('Action/CstLinkman');
	}
	public function cst_chance(){
		$assArr = $this->pcCstChance->cst_chance();
		echo json_encode($assArr);
	}
	public function cst_chance_show(){
			$assArr = $this->pcCstChance->cst_chance();
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/cst_chance_show.html');	
	}
	public function cst_chance_add(){
		$cusID=$this->_REQUEST("cusID");
		if(empty($_POST)){
			$salestage	= $this->WxCstDict->cst_dict_opt('salestage','salestage_id');
			$linkman	= $this->WxCstLinkman->cst_linkman_opt($cusID,'linkman',$optvalue='');
			$status	= $this->pcCstChance->cst_chance_status_select('status',$optvalue='');
			$assArr	= array("salestage"=>$salestage,"linkman"=>$linkman,"status"=>$status);
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/cst_chance_add.html');
		}else{
			$rtn=$this->pcCustomer->customer_add_save();
			$assArr	= array("msg"=>"操作成功","code"=>200,"gotourl"=>"wxapp/WxCustomer/customer_show/");
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('wxapp/alter.html');
		}
	}
}//end class
?>