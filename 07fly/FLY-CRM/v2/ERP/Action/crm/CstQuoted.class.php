<?php
/*
 * 产品报价类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstQuoted extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_quoted(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	   	   = $this->_REQUEST("cus_name");
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = " s.cusID=c.id ";

		$cusID = $this->_REQUEST("cusID");
		if(!empty($cusID) ){
			$where_str .=" and s.cusID='$cusID'";
		}
		if( !empty($searchValue) ){
			$where_str .=" and s.$searchKeyword like '%$searchValue%'";
		}
		if( !empty($cus_name) ){
			$where_str .=" and c.name like '%$cus_name%'";
		}		
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**************************************************************************
		$countSql    = "select c.name as cst_name ,s.* from cst_quoted as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,s.* from cst_quoted as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$operate=array();
		$money	=array();
		foreach($list as $key=>$row){
			$operate[$row["id"]]=$this->cst_quoted_operate($row["status"],$row["id"]);
			$money[$row["id"]]=_instance('Action/CstQuotedDetail')->cst_get_one_quoted_detail_money($row["id"]);
		}
		$assignArray = array('list'=>$list,
								"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,
								"operate"=>$operate,"money"=>$money
						);	
		return $assignArray;
		
	}
	
	public function cst_quoted_show(){
			$assArr  			= $this->cst_quoted();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_quoted_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_quoted/cst_quoted_show.html');	
	}		
	
	public function cst_quoted_show_box(){
			$assArr  			= $this->cst_quoted();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_quoted_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_quoted/cst_quoted_show_box.html');	
	}		
	
	public function cst_quoted_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('cst_quoted/cst_quoted_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$cusID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$quoted_userID  = $this->_REQUEST("our_id");
			
			$sql       	= "insert into cst_quoted(cusID,linkmanID,chanceID,quoted_userID,
												delivery_intro,payment_intro,transport_intro,title,intro,adt,create_userID) 
									values(
									'$cusID','$linkmanID','$chanceID','$ quoted_userID','$_POST[delivery_intro]',
										'$_POST[payment_intro]','$_POST[transport_intro]','$_POST[title]',
										'$_POST[intro]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	public function cst_quoted_get_one($id=""){
		if($id){
			$sql 		= "select * from cst_quoted where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}	
	
	public function cst_quoted_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_quoted where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users	 	= $this->L("User")->user_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance,"users"=>$users));
			$smarty->display('cst_quoted/cst_quoted_modify.html');	
		}else{//更新保存数据
		
			$cusID   = $this->_REQUEST("org_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$salemode    = $this->_REQUEST("salemode_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$quoted_userID  = $this->_REQUEST("our_id");
			$sql= "update cst_quoted set cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',quoted_userID='$quoted_userID',
			delivery_intro='$_POST[delivery_intro]',payment_intro='$_POST[payment_intro]',transport_intro='$_POST[transport_intro]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function cst_quoted_del(){
		$dt	  = date("Y-m-d H:i:s",time());
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_quoted where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstQuoted/cst_quoted_show/");	
	}
		
	public function cst_quoted_audit(){
		$dt	      = date("Y-m-d H:i:s",time());
		$id	  	  = $this->_REQUEST("id");
		$status	  = $this->_REQUEST("status");
		$sql= "update cst_quoted set status='$status',audit_userID='".SYS_USER_ID."',audit_dt='$dt' where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");				
	}
		
	public function cst_quoted_status(){
		return  array("1"=>"未审核","2"=>"同意","3"=>"否决");
	}

	
	public function cst_quoted_operate($status,$id){
		switch($status){
			case 1:
				$str="<a href='".ACT."/CstQuotedDetail/cst_quoted_detail_add/id/$id/' target='navTab' rel='cst_quoted_detail_add' title='产品报价明细' >编辑明细</a>
					  <a href='".ACT."/CstQuoted/cst_quoted_audit/status/2/id/$id/' target='ajaxTodo' title='确定要同意吗?'>同意</a>
					  <a href='".ACT."/CstQuoted/cst_quoted_audit/status/3/id/$id/' target='ajaxTodo' title='确定要拒决吗?'>拒决</a>";
				break;
			case 2:
				$str="<a href='".ACT."/CstQuoted/cst_quoted_show/id/$id' target='ajaxTodo' title='确定要生成订单吗?'>生成订单</a>";
				break;		
			case 3:
				$str="<a href='#'></a>";
				break;				
		}
		return $str;
	}	
}//end class
?>