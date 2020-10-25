<?php
/*
 * 付款计划类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinPayPlan extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function fin_pay_plan(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from fin_pay_plan';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_pay_plan  order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		
		$moneySql    = 'select sum(money) as sum_money from fin_pay_plan';
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		//供应商
		$supplier= array();
		$posorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["create_user"]	  = $this->L("User")->user_get_name($row['create_userID']);
				$supplier[$row['id']] = $this->L("Supplier")->supplier_get_name($row['supID']);
				$posorder[$row['id']] = $this->L("PosOrder")->pos_order_get_name($row['posID']);
			}
		}
		$assignArray = array('list'=>$list,'total_money'=>$moneyRs["sum_money"],
								'supplier'=>$supplier,'posorder'=>$posorder,
								'supplier'=>$supplier,'supplier'=>$supplier,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function fin_pay_plan_show(){
			$list	 = $this->fin_pay_plan();

			$smarty  = $this->setSmarty();
			$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_pay_plan/fin_pay_plan_show.html');	
	}		
	
	public function fin_pay_plan_add(){
		if(empty($_POST)){
			$smarty  = $this->setSmarty();
			//$smarty->assign();//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_pay_plan/fin_pay_plan_add.html');	
		}else{
			$posID		=$this->_REQUEST("order_id");
			$supID		=$this->_REQUEST("org_id");
			$stages		=$this->_REQUEST("stages");
			$plandate	=$this->_REQUEST("plandate");
			$planmoney	=$this->_REQUEST("plan_money");
			$intro		=$this->_REQUEST("intro");
			$sql= "insert into fin_pay_plan(posID,supID,plandate,money,stages,intro,adt,create_userID) 
								values('$posID','$supID','$plandate','$planmoney','$stages','$intro','".NOWTIME."','".SYS_USER_ID."');";
			if($this->C($this->cacheDir)->update($sql)>0){
				$this->L("Common")->ajax_json_success("操作成功","0","/FinPayPlan/fin_pay_plan_show/");	
			}
		}
	}		
	public function fin_pay_plan_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 			= "select * from fin_pay_plan where id='$id'";
			$one 			= $this->C($this->cacheDir)->findOne($sql);	
			
			$posorder		= $this->L("PosOrder")->pos_order_get_one($one["posID"]);
			$one["sup_name"]=$this->L("Supplier")->supplier_get_name($one["supID"]);
			$one["pos_name"]=$this->L("PosOrder")->pos_order_get_name($one["posID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"posorder"=>$posorder));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_pay_plan/fin_pay_plan_modify.html');	
		}else{
			$id			=$this->_REQUEST("id");;
			$posID		=$this->_REQUEST("order_id");
			$supID		=$this->_REQUEST("org_id");
			$blankID	=$this->_REQUEST("blank_id");
			$stages		=$this->_REQUEST("stages");
			$paydate	=$this->_REQUEST("paydate");
			$planmoney	=$this->_REQUEST("plan_money");	
			$intro		=$this->_REQUEST("intro");		
				
			$sql= "update fin_pay_plan set ifpay='YES',intro='$intro' where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			
			$this->L("FinPayRecord")->fin_pay_plan_record_add($id,$posID,$supID,$blankID,$paydate,
															$money=$planmoney,$stages,$intro
															);
			$this->L("Common")->ajax_json_success("操作成功","1","/FinPayPlan/fin_pay_plan_show/");			
		}
	}	
	public function fin_pay_plan_del(){
		$id=$this->_REQUEST("ids");
		$sql="delete from fin_pay_plan where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinPayPlan/fin_pay_plan_show/");	
	}	
		
}//end class
?>