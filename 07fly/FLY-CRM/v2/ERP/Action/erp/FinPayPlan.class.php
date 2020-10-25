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
		_instance('Action/sysmanage/Auth');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->supplier=_instance('Action/erp/SupSupplier');
		$this->contract=_instance('Action/erp/PosContract');
		$this->bank=_instance('Action/erp/FinBankAccount');
	}	
	
	public function fin_pay_plan(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$plan_date	= $this->_REQUEST("plan_date");
		$create_date= $this->_REQUEST("create_date");		
		$where_str	= " create_user_id in (".SYS_USER_VIEW.")";
		$supplier_name= $this->_REQUEST("supplier_name");
		$contract_name= $this->_REQUEST("contract_name");	
		$ifpay		 = $this->_REQUEST("ifpay");	
		
		if(!empty($ifpay)){
			$where_str	.= " and ifpay='$ifpay'";
		}
		if(!empty($supplier_name)){
			$where_str	.= " and supplier_name like '%$supplier_name%'";
		}
		if(!empty($contract_name)){
			$where_str	.= " and contract_name like '%$contract_name%'";
		}
		
		//到期时间
		if( !empty($plan_date) ){
			switch($plan_date){
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
			$where_str .=" and plan_date>'$date_range'";
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
		if( $orderField=='by_supplier' ){
			$order_by .=" supplier_id $orderDirection";
		}else if($orderField=='by_plandate'){
			$order_by .=" plan_date $orderDirection";
		}else if($orderField=='by_balance'){
			$order_by .=" balace $orderDirection";
		}else{
			$order_by .=" plan_id desc";
		}			
		$countSql    = "select plan_id from fin_pay_plan where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select * from fin_pay_plan  where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		
		$moneySql    = "select sum(money) as sum_money from fin_pay_plan where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		//供应商
		$supplier= array();
		$posorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
				$list[$key]['supplier']	=$this->supplier->sup_supplier_get_one($row['supplier_id']);
				$list[$key]['contract']	=$this->contract->pos_contract_get_one($row['contract_id']);
				$list[$key]['ifpay_arr']	=$this->fin_pay_plan_ifpay($row['ifpay']);
			}
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$moneyRs);	
		return $assignArray;
	}
	public function fin_pay_plan_json(){
		$list = $this->fin_pay_plan();
		echo json_encode($list);
	}	
	public function fin_pay_plan_show(){
		$list	 = $this->fin_pay_plan();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);
		$smarty->display('erp/fin_pay_plan_show.html');	
	}		
	
	public function fin_pay_plan_add(){
		$supplier_id= $this->_REQUEST("supplier_id");
		if(empty($_POST)){
			$supplier=$this->supplier->sup_supplier_list();
			$smarty = $this->setSmarty();
			$smarty->assign(array("supplier_id"=>$supplier_id,"supplier"=>$supplier));
			$smarty->display('erp/fin_pay_plan_add.html');	
		}else{
			$supplier_id=$this->_REQUEST("supplier_id");
			$contract_id=$this->_REQUEST("contract_id");
			$plan_money =$this->_REQUEST("plan_money");
			$plan_date =$this->_REQUEST("plan_date");
			$stages =$this->_REQUEST("stages");
			if(empty($supplier_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择供应商和采购合同");
				return false;
			}
			if(empty($plan_money)){
				$this->L("Common")->ajax_json_error("计划付款的金额不能为0");
				return false;
			}
			$into_data=array(
				'supplier_id'=>$supplier_id,
				'supplier_name'=>$this->_REQUEST("supplier_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$plan_money,
				'plan_date'=>$plan_date,
				'stages'=>$stages,
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('fin_pay_plan',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}	
		}
	}
	
	//修改付款
	public function fin_pay_plan_modify(){
		$plan_id = $this->_REQUEST("plan_id");
		if(empty($_POST)){
			$sql = "select * from fin_pay_plan where plan_id='$plan_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			$supplier=$this->supplier->sup_supplier_list();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"supplier"=>$supplier,"bank"=>$bank));
			$smarty->display('erp/fin_pay_plan_modify.html');	
		}else{
			$supplier_id=$this->_REQUEST("supplier_id");
			$contract_id=$this->_REQUEST("contract_id");
			$plan_money =$this->_REQUEST("plan_money");
			$plan_date =$this->_REQUEST("plan_date");
			$stages =$this->_REQUEST("stages");
			if(empty($supplier_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择供应商和采购合同");
				return false;
			}
			if(empty($plan_money)){
				$this->L("Common")->ajax_json_error("计划付款的金额不能为0");
				return false;
			}
			$into_data=array(
				'supplier_id'=>$supplier_id,
				'supplier_name'=>$this->_REQUEST("supplier_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$plan_money,
				'plan_date'=>$plan_date,
				'stages'=>$stages
			);
			$this->C($this->cacheDir)->modify('fin_pay_plan',$into_data,"plan_id='$plan_id'");
			$this->L("Common")->ajax_json_success("操作成功");
			
		}
	}
	
	//确认付款
	public function fin_pay_plan_sure(){
		$plan_id = $this->_REQUEST("plan_id");
		if(empty($_POST)){
			$sql = "select * from fin_pay_plan where plan_id='$plan_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			$supplier=$this->supplier->sup_supplier_list();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"supplier"=>$supplier,"bank"=>$bank));
			$smarty->display('erp/fin_pay_plan_sure.html');	
		}else{
			$this->C($this->cacheDir)->modify('fin_pay_plan',array('ifpay'=>1),"plan_id='$plan_id'");				
		}
	}	
	public function fin_pay_plan_del(){
		$plan_id=$this->_REQUEST("plan_id");
		$sql="delete from fin_pay_plan where plan_id in ($plan_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	//合同状态
	public function fin_pay_plan_ifpay($key=null){
		$data=array(
			"-1"=>array(
				 		'status_name'=>'未付款',
				 		'status_name_html'=>'<span class="label label-warning">未付款<span>',
						'status_operation' => array(
                   '0' => array(
                        'act' => 'paymoney',
                        'color' => '#7266BA',
                        'name' => '付款'
                    ),
							'1' => array(
                        'act' => 'modify',
                        'color' => '#23B7E5',
                        'name' => '修改'
                    ),
							'2' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    ),
                ),
				),
			"1"=>array(
				 		'status_name'=>'已付款',
				 		'status_name_html'=>'<span class="label label-success">已付款<span>',
						'status_operation' => array(
                 	'0' => array(
                        'act' => 'delete',
                        'color' => '#F05050',
                        'name' => '删除'
                    )
                 ),
					)
		);
		return ($key)?$data[$key]:$data;
	}
		
}//end class
?>