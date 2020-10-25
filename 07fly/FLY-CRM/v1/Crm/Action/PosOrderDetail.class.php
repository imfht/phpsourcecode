<?php
/*
 * 采购订单明细类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class PosOrderDetail extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
		//$this->CstQuoted  = _instance('Action/CstQuoted');
	}	
	
	public function pos_order_detail(){
	
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
		$where_str = " create_userID = '".SYS_USER_VIEW."'";

		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**************************************************************************
		$countSql    = "select * from pos_order_detail  where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select  * from pos_order_detail where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage
						);	
		return $assignArray;
		
	}
	
	public function pos_order_detail_show(){
			$assArr  			= $this->pos_order_detail();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('pos_order_detail/pos_order_detail_show.html');	
	}	
	public function sal_get_one_order_detail_money($posID=""){
		$rtArr  =array();
		$where  =empty($posID)?"":" where posID='$posID'";
		$sql	="select sum(money) as total from pos_order_detail $where";
		$list	=$this->C($this->cacheDir)->findOne($sql);	
		return $list["total"];
	}	
	public function sal_get_one_order_detail($posID=""){
		$rtArr  =array();
		$where  =empty($posID)?"":" where posID='$posID'";
		$sql	="select * from pos_order_detail $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}		
	public function pos_order_detail_add(){
		$posID=$this->_REQUEST("id");	
		if(empty($_POST)){
			$order  	= $this->L("PosOrder")->Pos_order_get_one($posID);
			$list   	= $this->sal_get_one_order_detail($posID);
			$supplier   = $this->L("Supplier")->supplier_arr();
			$linkman    = $this->L("SupLinkman")->sup_linkman_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users		= $this->L("User")->user_arr();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$order,"supplier"=>$supplier,"linkman"=>$linkman,"chance"=>$chance,'list'=>$list,'users'=>$users));
			$smarty->display('pos_order_detail/pos_order_detail_add.html');	
		}else{
			$posID=$this->_REQUEST("id");	
			$pos_number=$this->_REQUEST("pos_number");	
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
			
			$sql = "delete from pos_order_detail where posID='$posID'";
			if($this->C($this->cacheDir)->update($sql)<0){
				$this->C($this->cacheDir)->rollback();
			}		
			for ($irow=0 ; $irow<=count($name)-1; $irow++){
				$sql = "insert into pos_order_detail(
										posID,pos_number,pro_number,name,model,norm,price,rebate,number,money,adt,create_userID) 
									values(
										'$posID','$pos_number','$pro_number[$irow]','$name[$irow]','$model[$irow]','$norm[$irow]',
										'$price[$irow]','$orgDiscount[$irow]','$orgCount[$irow]','$orgTotal[$irow]','$dt','".SYS_USER_ID."');";
				if($this->C($this->cacheDir)->update($sql)<=0){
					$this->C($this->cacheDir)->rollback();
				}	
			}
			$this->C($this->cacheDir)->commit();	
			$this->L("Common")->ajax_json_success("操作成功");		
			
		}
	}		
	
	
	public function pos_order_detail_modify(){
		$id	= $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from pos_order_detail where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$supplier   = $this->L("Supplier")->supplier_arr();
			$linkman    = $this->L("SupLinkman")->sup_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"supplier"=>$supplier,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance));
			$smarty->display('pos_order_detail/pos_order_detail_modify.html');	
		}else{//更新保存数据
		
			$cusID   = $this->_REQUEST("org_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$salemode    = $this->_REQUEST("salemode_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$sql= "update pos_order_detail set cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',delivery_intro='$_POST[delivery_intro]',
						payment_intro='$_POST[payment_intro]',transport_intro='$_POST[transport_intro]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function pos_order_detail_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from pos_order_detail where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/PosOrderDetail/pos_order_detail_show/");	
	}			
}// end class
?>