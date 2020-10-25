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
		_instance('Action/sysmanage/Auth');
		$this->bank=_instance('Action/erp/FinBankAccount');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->supplier=_instance('Action/erp/SupSupplier');
		$this->contract=_instance('Action/erp/PosContract');
		$this->flow=_instance('Action/erp/FinFlowRecord');
	}	
	public function fin_pay_record($id=null){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
	
		$contract_id 	= $this->_REQUEST("contract_id");
		$customer_id 	= $this->_REQUEST("customer_id");
		$pay_date		= $this->_REQUEST("pay_date");
		$create_date	= $this->_REQUEST("create_date");		
		$supplier_name	= $this->_REQUEST("supplier_name");
		$contract_name	= $this->_REQUEST("contract_name");	
		
		$where_str	 =" record_id>0 ";
		if($contract_id){
			$where_str .=" and contract_id='$contract_id'";
		}
        if($customer_id){
            $where_str .=" and customer_id='$customer_id'";
        }
		if(!empty($supplier_name)){
			$where_str	.= " and supplier_name like '%$supplier_name%'";
		}
		if(!empty($contract_name)){
			$where_str	.= " and contract_name like '%$contract_name%'";
		}
		
		//到期时间
		if( !empty($pay_date) ){
			switch($pay_date){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("-3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("-7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("-15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("-1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("-3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("-6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("-12 month",time()));	
					break;
			}
			$where_str .=" and pay_date>'$date_range'";
		}
		//创建日期
		if( !empty($create_date) ){
			switch($create_date){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("+3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("+7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("+15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("+1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("+3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("+6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("+12 month",time()));	
					break;
					
			}
			$where_str .=" and create_time<'$date_range'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_rece' ){
			$order_by .=" rece_money $orderDirection";
		}else if($orderField=='by_pay'){
			$order_by .=" pay_money $orderDirection";
		}else if($orderField=='by_balance'){
			$order_by .=" balace $orderDirection";
		}else{
			$order_by .=" record_id desc";
		}			
		
		$countSql    = "select record_id from fin_pay_record where $where_str ";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		
		$sql		 = "select * from fin_pay_record  where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["bank_arr"]	  	= $this->bank->fin_bank_accoun_get_one($row['bank_id']);
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
				$list[$key]['supplier']	=$this->supplier->sup_supplier_get_one($row['supplier_id']);
				$list[$key]['contract']	=$this->contract->pos_contract_get_one($row['contract_id']);
			}
		}
		
		$moneySql    = "select sum(money) as total_money from fin_pay_record  where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);		
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$moneyRs);	
		return $assignArray;
	}
	public function fin_pay_record_json(){
		$list	 = $this->fin_pay_record();
		echo json_encode($list);	
	}	
	public function fin_pay_record_show(){
		$list	 = $this->fin_pay_record();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);
		$smarty->display('erp/fin_pay_record_show.html');	
	}	

	public function fin_pay_record_add(){
		$supplier_id= $this->_REQUEST("supplier_id");
		if(empty($_POST)){
			$supplier=$this->supplier->sup_supplier_list();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("supplier_id"=>$supplier_id,"supplier"=>$supplier,"bank"=>$bank));
			$smarty->display('erp/fin_pay_record_add.html');	
		}else{
			$supplier_id=$this->_REQUEST("supplier_id");
			$contract_id=$this->_REQUEST("contract_id");
			$contract_pay_money =$this->_REQUEST("contract_pay_money");
			$contract_zero_money =$this->_REQUEST("contract_zero_money");
			$contract_owe_money =$this->_REQUEST("contract_owe_money");
			
			$pay_money =$this->_REQUEST("pay_money");
			$zero_money =$this->_REQUEST("zero_money");
			$pay_date =$this->_REQUEST("pay_date");
			$stages =$this->_REQUEST("stages");
			$bank_id =$this->_REQUEST("bank_id");
			if(empty($supplier_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择供应商和采购合同");
				return false;
			}
			if(empty($pay_money)){
				$this->L("Common")->ajax_json_error("付款的金额不能为0");
				return false;
			}
			$owe_money=$contract_owe_money-$pay_money-$zero_money;
			if($owe_money<0){
				$this->L("Common")->ajax_json_error("本次付款的金额和去零金额不能超过 $contract_owe_money");
				return false;				
			}
			$into_data=array(
				'supplier_id'=>$supplier_id,
				'supplier_name'=>$this->_REQUEST("supplier_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$pay_money,
				'zero_money'=>$zero_money,
				'pay_date'=>$pay_date,
				'stages'=>$stages,
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			//插入记录
			$this->C($this->cacheDir)->insert('fin_pay_record',$into_data);
			
			//更改采购订单状态
			$sql ="select * from pos_contract where contract_id='$contract_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			if($one){
				$contract_money		=$one["money"];
				$contract_pay_money =$one["pay_money"];
				if(($pay_money+$zero_money)>=$contract_owe_money){
					$pay_status=3;//已付
				}else{
					$pay_status=2;//未付
				}
				$upt_contract=array(
					'pay_status'=>$pay_status,
					'pay_money'=>$contract_pay_money+$pay_money,
					'zero_money'=>$contract_zero_money+$zero_money,
					'owe_money'=>$owe_money,
				);
				$this->C($this->cacheDir)->modify('pos_contract',$upt_contract,"contract_id='$contract_id'");				
			}
			//更新合同执行状态
			$this->contract->pos_contract_modify_status($contract_id);					
			//增加财务流水
			$this->flow->fin_flow_record_add('pay',$pay_money,$bank_id,$contract_id,'pos_contract');
			
			$this->L("Common")->ajax_json_success("操作成功");	
		
		}
	}		
	//删除付款记录
	public function fin_pay_record_del(){
		$record_id=$this->_REQUEST("record_id");
		$record_arr=explode(",",$record_id);
		foreach($record_arr as $id){
			$sql ="select * from fin_pay_record where record_id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			$contract_id=$one['contract_id'];
			$bank_id=$one['bank_id'];
			$money=$one['money'];
			$zero_money=$one['zero_money'];
			
			//删除记录
			$this->C($this->cacheDir)->delete('fin_pay_record',"record_id='$id'");	
			
			//更改采购订单状态
			$sql ="select * from pos_contract where contract_id='$contract_id'";
			$con = $this->C($this->cacheDir)->findOne($sql);
			
			if($one){
				$contract_money		=$con["money"];
				$contract_pay_money =$con["pay_money"];
				$contract_owe_money =$con["owe_money"];
				$contract_zero_money =$con["zero_money"];
				
				if(($contract_owe_money+$money+$zero_money)>=$contract_money){
					$pay_status=1;//未付
				}else{
					$pay_status=2;//部分
				}
				$upt_contract=array(
					'pay_status'=>$pay_status,
					'pay_money'=>$contract_pay_money-$money,
					'zero_money'=>$contract_zero_money-$zero_money,
					'owe_money'=>$contract_owe_money+$money+$zero_money,
				);
				$this->C($this->cacheDir)->modify('pos_contract',$upt_contract,"contract_id='$contract_id'");				
			}
			//更新合同执行状态
			$this->contract->pos_contract_modify_status($contract_id);		
			//增加财务流水
			$this->flow->fin_flow_record_add('rece',$money,$bank_id,$contract_id,'pos_contract');
		}
		$this->L("Common")->ajax_json_success("操作成功");	
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