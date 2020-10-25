<?php	 
/*
 * 回款记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinReceRecord extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->customer=_instance('Action/crm/CstCustomer');
		$this->contract=_instance('Action/crm/SalContract');
		$this->bank=_instance('Action/erp/FinBankAccount');
		$this->flow=_instance('Action/erp/FinFlowRecord');
		$this->comm=_instance('Extend/Common');
	}	
	public function fin_rece_record($id=null){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
	
		$contract_id 	= $this->_REQUEST("contract_id");
		$customer_id 	= $this->_REQUEST("customer_id");
		$back_date		= $this->_REQUEST("back_date");
		$create_date	= $this->_REQUEST("create_date");		
		$customer_name	= $this->_REQUEST("customer_name");
		$contract_name	= $this->_REQUEST("contract_name");	
		
		$where_str	 =" record_id>0 ";
		if($contract_id){
			$where_str .=" and contract_id='$contract_id'";
		}
		if($customer_id){
			$where_str .=" and customer_id='$customer_id'";
		}
		if(!empty($customer_name)){
			$where_str	.= " and customer_name like '%$customer_name%'";
		}
		if(!empty($contract_name)){
			$where_str	.= " and contract_name like '%$contract_name%'";
		}
		
		//到期时间
		if( !empty($back_date) ){
			$date_range =$this->comm->date_range('-1',$back_date);
			$where_str .=" and back_date>'$date_range'";
		}
		//创建日期
		if( !empty($create_date) ){
			$date_range =$this->comm->date_range('-1',$create_date);
			$where_str .=" and create_time<'$date_range'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_rece' ){
			$order_by .=" rece_money $orderDirection";
		}else if($orderField=='by_pay'){
			$order_by .=" back_money $orderDirection";
		}else if($orderField=='by_balance'){
			$order_by .=" balace $orderDirection";
		}else{
			$order_by .=" record_id desc";
		}			
		
		$countSql   = "select * from fin_rece_record where $where_str ";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		
		$sql		 = "select * from fin_rece_record  where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["bank_arr"]	  	= $this->bank->fin_bank_accoun_get_one($row['bank_id']);
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
				$list[$key]['customer']	=$this->customer->cst_customer_get_one($row['customer_id']);
				$list[$key]['contract']	=$this->contract->sal_contract_get_one($row['contract_id']);
			}
		}
		
		$moneySql    = "select sum(money) as total_money from fin_rece_record  where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);		
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$moneyRs);	
		return $assignArray;
	}
	public function fin_rece_record_json(){
		$list	 = $this->fin_rece_record();
		echo json_encode($list);	
	}	
	public function fin_rece_record_show(){
		$list	 = $this->fin_rece_record();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);
		$smarty->display('erp/fin_rece_record_show.html');	
	}	

	public function fin_rece_record_add(){
		$customer_id= $this->_REQUEST("customer_id");
		$contract_id= $this->_REQUEST("contract_id");
		if(empty($_POST)){
			$customer	=$this->customer->cst_customer_list();
			$bank		=$this->bank->fin_bank_accoun_select();
			$contract	=$this->contract->sal_contract_get_one($contract_id);
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer,"contract"=>$contract,"bank"=>$bank));
			$smarty->display('erp/fin_rece_record_add.html');	
		}else{
			$customer_id=$this->_REQUEST("customer_id");
			$contract_id=$this->_REQUEST("contract_id");
			$contract_back_money =$this->_REQUEST("contract_back_money");
			$contract_zero_money =$this->_REQUEST("contract_zero_money");
			$contract_owe_money =$this->_REQUEST("contract_owe_money");
			
			$back_money =$this->_REQUEST("back_money");
			$zero_money =$this->_REQUEST("zero_money");
			$back_date =$this->_REQUEST("back_date");
			$stages =$this->_REQUEST("stages");
			$bank_id =$this->_REQUEST("bank_id");
			if(empty($customer_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择回款客户名称和销售合同");
				return false;
			}
			if(empty($back_money)){
				$this->L("Common")->ajax_json_error("回款的金额不能为0");
				return false;
			}
			$owe_money=$contract_owe_money-$back_money-$zero_money;
			if($owe_money<0){
				$this->L("Common")->ajax_json_error("本次回款的金额和去零金额不能超过 $contract_owe_money");
				return false;				
			}
			$into_data=array(
				'customer_id'=>$customer_id,
				'customer_name'=>$this->_REQUEST("customer_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$back_money,
				'zero_money'=>$zero_money,
				'back_date'=>$back_date,
				'stages'=>$stages,
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			//插入记录
			$this->C($this->cacheDir)->insert('fin_rece_record',$into_data);
			
			//更改采购订单状态
			$sql ="select * from sal_contract where contract_id='$contract_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			if($one){
				$contract_money		 =$one["money"];
				$contract_back_money =$one["back_money"];
				if(($back_money+$zero_money)>=$contract_owe_money){
					$back_status=3;//已付
				}else{
					$back_status=2;//未付
				}
				$upt_contract=array(
					'back_status'=>$back_status,
					'back_money'=>$contract_back_money+$back_money,
					'zero_money'=>$contract_zero_money+$zero_money,
					'owe_money'=>$owe_money,
				);
				$this->C($this->cacheDir)->modify('sal_contract',$upt_contract,"contract_id='$contract_id'");				
			}
			//更新合同状态
			$this->contract->sal_contract_modify_status($contract_id);
			//增加财务流水
			$this->flow->fin_flow_record_add('rece',$back_money,$bank_id,$contract_id,'sal_contract');
			
			$this->L("Common")->ajax_json_success("操作成功");	
		
		}
	}		
	//删除回款记录
	public function fin_rece_record_del(){
		$record_id=$this->_REQUEST("record_id");
		$record_arr=explode(",",$record_id);
		foreach($record_arr as $id){
			$sql ="select * from fin_rece_record where record_id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			$contract_id=$one['contract_id'];
			$bank_id=$one['bank_id'];
			$money=$one['money'];
			$zero_money=$one['zero_money'];
			
			//删除记录
			$this->C($this->cacheDir)->delete('fin_rece_record',"record_id='$id'");	
			
			//更改采购订单状态
			$sql ="select * from sal_contract where contract_id='$contract_id'";
			$con = $this->C($this->cacheDir)->findOne($sql);
			
			if($one){
				$contract_money		=$con["money"];
				$contract_back_money =$con["back_money"];
				$contract_owe_money =$con["owe_money"];
				$contract_zero_money =$con["zero_money"];
				
				if(($contract_owe_money+$money+$zero_money)>=$contract_money){
					$back_status=1;//未付
				}else{
					$back_status=2;//部分
				}
				$upt_contract=array(
					'back_status'=>$back_status,
					'back_money'=>$contract_back_money-$money,
					'zero_money'=>$contract_zero_money-$zero_money,
					'owe_money'=>$contract_owe_money+$money+$zero_money,
				);
				$this->C($this->cacheDir)->modify('sal_contract',$upt_contract,"contract_id='$contract_id'");				
			}
			//更新合同执行状态
			$this->contract->sal_contract_modify_status($contract_id);
			//增加财务流水
			$this->flow->fin_flow_record_add('pay',$money,$bank_id,$contract_id,'sal_contract');
		}
		$this->L("Common")->ajax_json_success("操作成功");	
	}
		
}//
?>