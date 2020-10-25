<?php
/*
 * 付款记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinPayRecord extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	public function fin_pay_record($id=null){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		$posID		 =$this->_REQUEST("posID");
		$where_str	 = " 0=0 ";
		if($id){
			$where_str  = "id in($id)";
		}else{
			$where_str  = "id>0";
		}
		
		if($posID){
			$where_str .=" and posID='$posID'";
		}
		
		
		$countSql    = "select id from fin_pay_record where $where_str ";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		
		$moneySql    = "select sum(money) as sum_money from fin_pay_record  where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_pay_record  where $where_str 
						order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		//供应商
		$supplier= array();
		$posorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["create_user"]	  = $this->L("User")->user_get_name($row['create_userID']);
				$list[$key]["blankaccount"]	  = $this->L("FinBankAccount")->fin_bank_accoun_get_name($row['blankID']);
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
	public function fin_pay_record_show(){
			$list	 = $this->fin_pay_record();
			$smarty  = $this->setSmarty();
			$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_pay_record/fin_pay_record_show.html');	
	}	

	//主要用于BOX局部调用
	public function fin_pay_record_show_box(){
			$list	 = $this->fin_pay_record();
			$smarty = $this->setSmarty();
			$smarty->assign($list);
			$smarty->display('fin_pay_record/fin_pay_record_show_box.html');	
	}		
	
	public function fin_pay_record_add(){
		if(empty($_POST)){
			$smarty  = $this->setSmarty();
			//$smarty->assign();//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_pay_record/fin_pay_record_add.html');	
		}else{
			$id			=$this->_REQUEST("id");;
			$posID		=$this->_REQUEST("order_id");
			$supID		=$this->_REQUEST("org_id");
			$blankID	=$this->_REQUEST("blank_id");
			$stages		=$this->_REQUEST("stages");
			$paydate	=$this->_REQUEST("paydate");
			$paymoney	=$this->_REQUEST("order_now_pay_money");//支付金额	
			$intro		=$this->_REQUEST("intro");	
			
			$sql= "insert into fin_pay_record(posID,supID,paydate,money,stages,blankID,
												intro,adt,create_userID) 
									values('$posID','$supID','$paydate','$paymoney','$stages','$blankID',
											'$intro','".NOWTIME."','".SYS_USER_ID."');";
			if($this->C($this->cacheDir)->update($sql)>0){
				$this->L("PosOrder")->pos_order_pay_modify($posID,$new_money=$paymoney);
				$this->L("FinFlowRecord")->fin_flow_record_add('pay',$paymoney,$blankID,$posID,'pos_order');
				$this->L("Common")->ajax_json_success("操作成功","2","/FinPayRecord/fin_pay_record_show/");	
			}
		}
	}		
	public function fin_pay_record_del(){
		$id=$this->_REQUEST("ids");
		$sql="delete from fin_pay_record where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinPayRecord/fin_pay_record_show/");	
	}
	
	//付款记录添加付款计划 to 付款记录
	public function fin_pay_plan_record_add($planID,$posID,$supID,$blankID,$paydate,$money,$stages,$intro){
		$sql= "insert into fin_pay_record(
					planID,posID,supID,blankID,paydate,money,stages,intro,adt,create_userID) 
				values(
					'$planID','$posID','$supID','$blankID','$paydate','$money','$stages','$intro','".NOWTIME."','".SYS_USER_ID."');";
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("PosOrder")->pos_order_pay_modify($posID,$new_money=$money);//更新订单
			$this->L("FinFlowRecord")->fin_flow_record_add('pay',$money,$blankID,$posID,'pos_order');//添加流水
			return true;
		}else{
			return false;	
		}	
	}

//关联业务选择
	public function fin_pay_get_business(){
		$order	=$this->L("PosOrder")->pos_order_select('pay_status');
/*		print_r($order);
		print_r($contr);*/
/*            [id] => 4
            [name] => 100
            [money] => 1000
            [bill_money] => 0
            [zero_money] => 0
            [back_money] => 0
            [now_back_money] => 1000*/
		$rtnArr	=array();
		$key	=0;
		foreach($order as $row){
			$rtnArr[$key]			=$row;
			$rtnArr[$key]["type"]	="pos_order";
			$key++;
		}
		//print_r($rtnArr);
		echo json_encode($rtnArr);
	}
		
}//
?>