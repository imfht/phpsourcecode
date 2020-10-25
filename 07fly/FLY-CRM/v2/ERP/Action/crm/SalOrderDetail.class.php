<?php
/*
 * 销售订单明细类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class SalOrderDetail extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
		//$this->CstQuoted  = _instance('Action/CstQuoted');
	}	
	
	public function sal_order_detail(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	   	   = $this->_REQUEST("cus_name");
		$orderID	   	   = $this->_REQUEST("orderID");
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = " create_userID = '".SYS_USER_VIEW."'";

		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}	
//		if( !empty($bdt) ){
//			$where_str .=" and adt >= '$bdt'";
//		}			
//		if( !empty($edt) ){
//			$where_str .=" and adt < '$edt'";
//		}

		if( !empty($orderID) ){
			$where_str .=" and orderID='$orderID'";
		}	
			
		//**************************************************************************
		$countSql    = "select * from sal_order_detail  where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;

		$moneySql    = "select sum(money) as sum_money from sal_order_detail where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$sql		 = "select  * from sal_order_detail where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"orderID"=>$orderID,"total_money"=>$moneyRs["sum_money"],
								"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);	
		return $assignArray;
		
	}
	
	public function sal_order_detail_show(){
			$assArr  			= $this->sal_order_detail();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_order_detail/sal_order_detail_show.html');	
	}	
	
	//box中显示
	public function sal_order_detail_show_box(){
			$assArr  			= $this->sal_order_detail();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_order_detail/sal_order_detail_show_box.html');	
	}	
	
	public function sal_get_one_order_detail_money($orderID=""){
		$rtArr  =array();
		$where  =empty($orderID)?"":" where orderID='$orderID'";
		$sql	="select sum(money) as total from sal_order_detail $where";
		$list	=$this->C($this->cacheDir)->findOne($sql);	
		return $list["total"];
	}	
	public function sal_get_one_order_detail($orderID=""){
		$rtArr  =array();
		$where  =empty($orderID)?"":" where orderID='$orderID'";
		$sql	="select * from sal_order_detail $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}	
	//订单明细增加	
	public function sal_order_detail_add(){
		$orderID=$this->_REQUEST("id");	
		if(empty($_POST)){
			$order  	= $this->L("SalOrder")->sal_order_get_one($orderID);
			$list   	= $this->sal_get_one_order_detail($orderID);
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users		= $this->L("User")->user_arr();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$order,"customer"=>$customer,"linkman"=>$linkman,"chance"=>$chance,'list'=>$list,'users'=>$users));
			$smarty->display('sal_order_detail/sal_order_detail_add.html');	
		}else{
			$orderID	= $this->_REQUEST("id");	
			$ord_number	= $this->_REQUEST("ord_number");	
			$name 		= $this->_REQUEST("items_name");	
			$pro_number = $this->_REQUEST("items_pro_number");	
			$model 		= $this->_REQUEST("items_model");	
			$norm 		= $this->_REQUEST("items_norm");	
			$price 		= $this->_REQUEST("items_price");
			$orgCount 	= $this->_REQUEST("items_orgCount");		
			$orgDiscount = $this->_REQUEST("items_orgDiscount");	
			$orgTotal 	= $this->_REQUEST("items_orgTotal");
			$total_amount = $this->_REQUEST("total_amount");
			$dt	     	= date("Y-m-d H:i:s",time());

			$this->C($this->cacheDir)->begintrans();
			
			$sql = "delete from sal_order_detail where orderID='$orderID'";
			if($this->C($this->cacheDir)->update($sql)<0){
				$this->C($this->cacheDir)->rollback();
			}	
			$pay_total=0;
			for ($irow=0 ; $irow<=count($name)-1; $irow++){
				$sql = "insert into sal_order_detail(
										orderID,ord_number,pro_number,name,model,norm,price,rebate,number,money,adt,create_userID) 
									values(
										'$orderID','$ord_number','$pro_number[$irow]','$name[$irow]','$model[$irow]','$norm[$irow]',
										'$price[$irow]','$orgDiscount[$irow]','$orgCount[$irow]','$orgTotal[$irow]','$dt','".SYS_USER_ID."');";
				if($this->C($this->cacheDir)->update($sql)<=0){
					$this->C($this->cacheDir)->rollback();
				}
				$pay_total=$pay_total+$orgTotal[$irow];	
			}
			
			//更改订单金额
			$sql= "update sal_order set money='$pay_total' where id='$orderID'";
			if($this->C($this->cacheDir)->update($sql)<=0){
				$this->C($this->cacheDir)->rollback();
			}
			
			$this->C($this->cacheDir)->commit();	
			$this->L("Common")->ajax_json_success("操作成功");		
			
		}
	}		
	
	//明细修改
	public function sal_order_detail_modify(){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from sal_order_detail where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance));
			$smarty->display('sal_order_detail/sal_order_detail_modify.html');	
		}else{//更新保存数据
		
			$cusID   = $this->_REQUEST("org_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$salemode    = $this->_REQUEST("salemode_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$sql= "update sal_order_detail set cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',delivery_intro='$_POST[delivery_intro]',
						payment_intro='$_POST[payment_intro]',transport_intro='$_POST[transport_intro]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
	//明细删除
	public function sal_order_detail_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from sal_order_detail where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/SalOrderDetail/sal_order_detail_show/");	
	}		
}
?>