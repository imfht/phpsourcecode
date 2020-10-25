<?php
/*
 * 产品报价详细类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstQuotedDetail extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_quoted_detail(){
	
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
		$countSql    = "select c.name as cst_name ,s.* from cst_quoted_detail as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,s.* from cst_quoted_detail as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$operate[$row["id"]]=$this->cst_quoted_detail_operate($row["status"],$row["id"]);
		}
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage,"operate"=>$operate
						);	
		return $assignArray;
		
	}
	
	public function cst_quoted_detail_show(){
			$assArr  			= $this->cst_quoted_detail();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_quoted_detail_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_quoted_detail/cst_quoted_detail_show.html');	
	}	
	public function cst_get_one_quoted_detail_money($quotedID=""){
		$rtArr  =array();
		$where  =empty($quotedID)?"":" where quotedID='$quotedID'";
		$sql	="select sum(money) as total from cst_quoted_detail $where";
		$list	=$this->C($this->cacheDir)->findOne($sql);	
		return $list["total"];
	}	
	public function cst_get_one_quoted_detail($quotedID=""){
		$rtArr  =array();
		$where  =empty($quotedID)?"":" where quotedID='$quotedID'";
		$sql	="select * from cst_quoted_detail $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}		
	public function cst_quoted_detail_add(){
		$quotedID=$this->_REQUEST("id");	
		if(empty($_POST)){
			$quote  = _instance('Action/CstQuoted')->cst_quoted_get_one($quotedID);
			$list   = $this->cst_get_one_quoted_detail($quotedID);
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$quote,"customer"=>$customer,"linkman"=>$linkman,"chance"=>$chance,'list'=>$list));
			$smarty->display('cst_quoted_detail/cst_quoted_detail_add.html');	
		}else{
			$quotedID=$this->_REQUEST("id");	
			$name = $this->_REQUEST("items_name");	
			$pro_number = $this->_REQUEST("items_pro_number");	
			$model = $this->_REQUEST("items_model");	
			$norm = $this->_REQUEST("items_norm");	
			$price = $this->_REQUEST("items_price");
			$orgCount = $this->_REQUEST("items_orgCount");		
			$orgDiscount = $this->_REQUEST("items_orgDiscount");	
			$orgTotal = $this->_REQUEST("items_orgTotal");
			$total_amount = $this->_REQUEST("total_amount");
			$dt	     	= date("Y-m-d H:i:s",time());

			$this->C($this->cacheDir)->begintrans();
			
			$sql = "delete from cst_quoted_detail where quotedID='$quotedID'";
			if($this->C($this->cacheDir)->update($sql)<0){
				$this->C($this->cacheDir)->rollback();
			}		
			for ($irow=0 ; $irow<=count($name)-1; $irow++){
				$sql = "insert into cst_quoted_detail(
										quotedID,pro_number,name,model,norm,price,rebate,number,money,adt) 
									values(
										'$quotedID','$pro_number[$irow]','$name[$irow]','$model[$irow]','$norm[$irow]',
										'$price[$irow]','$orgDiscount[$irow]','$orgCount[$irow]','$orgTotal[$irow]','$dt');";
				if($this->C($this->cacheDir)->update($sql)<=0){
					$this->C($this->cacheDir)->rollback();
				}	
			}
			$this->C($this->cacheDir)->commit();	
			$this->L("Common")->ajax_json_success("操作成功");		
			
		}
	}		
	
	public function cst_quoted_detail_modify(){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_quoted_detail where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance));
			$smarty->display('cst_quoted_detail/cst_quoted_detail_modify.html');	
		}else{//更新保存数据
		
			$cusID   = $this->_REQUEST("org_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$salemode    = $this->_REQUEST("salemode_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$sql= "update cst_quoted_detail set cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',delivery_intro='$_POST[delivery_intro]',
						payment_intro='$_POST[payment_intro]',transport_intro='$_POST[transport_intro]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function cst_quoted_detail_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_quoted_detail where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstQuotedDetail/cst_quoted_detail_show/");	
	}			
}//end class
?>