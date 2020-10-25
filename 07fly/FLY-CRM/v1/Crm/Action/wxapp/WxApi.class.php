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
class Api extends Action {

	private $cacheDir =''; //缓存目录
	private $menulist ='';
	public function __construct() {
//		$username 	 = $this->_REQUEST("username");
//		$password 	 = $this->_REQUEST("password");
//		$sql = "select * from fly_sys_user where account='$username' and password='$password'";	
//		$one = $this->C($this->cacheDir)->findOne($sql);
//		if($one){
//			$_SESSION["CRM"]["USER"]["menu"]		="1";
//			$_SESSION["CRM"]["USER"]["method"]		="1";	
//		}
//		$role=_instance('Action/User')->user_get_power($one["id"]);	
//		$_SESSION["WXAPP"]["USER"]["ViewMenuId"] = array_unique(explode(",",implode(",",($role["SYS_MENU"]) ) ));
//		$view_menu_id = implode(",",$_SESSION["WXAPP"]["USER"]["ViewMenuId"]);
//		
//		$sql = "select * from fly_sys_menu where visible='1' and id in ($view_menu_id)  order by sort asc,id desc;";
//		$list = $this->C( $this->cacheDir )->findAll( $sql );
//		$data = _instance( 'Extend/Tree' )->arrToTree( $list, 0 );
//		
//		$this->menulist=$data;
	}
	
	//应用菜单栏目
	public function menulist(){
		$allMenu=$this->menulist;
		$x=array_column($allMenu,'parentID');
		
		$i=0;
		foreach($x as $key1=>$one){//三维合并成二维栏目
			$rtn1[$i] = $one;
			foreach($one as $key2=>$two){
				$rtn2[$i] = $two;
				$rtn2[$i]['open']='false';
				$i++;
			}
		}
		//$rtn=array_column($allMenu,'parentID');
		//print_r($rtn2);
		echo json_encode($rtn2);
	}
	
	public function mobile(){
		$smarty  = $this->setSmarty();
/*		$smarty->assign($assArr);*/
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

	public function customer_show(){
			$assArr  		= $this->L('Customer')->customer();
			$assArr["dict"] = $this->L("CstDict")->cst_dict_arr();
			$smarty  		= $this->setSmarty();
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