<?php
/*
 * 收款记划类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinRecePlan extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->customer=_instance('Action/crm/CstCustomer');
		$this->contract=_instance('Action/crm/SalContract');
		$this->bank=_instance('Action/erp/FinBankAccount');
	}	
	
	public function fin_rece_plan(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$plan_date	= $this->_REQUEST("plan_date");
		$create_date= $this->_REQUEST("create_date");		
		$where_str	= " create_user_id in (".SYS_USER_VIEW.")";
		$customer_name= $this->_REQUEST("customer_name");
		$contract_name= $this->_REQUEST("contract_name");	
		$ifpay		 = $this->_REQUEST("ifpay");	
		
		if(!empty($ifpay)){
			$where_str	.= " and ifpay='$ifpay'";
		}
		if(!empty($customer_name)){
			$where_str	.= " and customer_name like '%$customer_name%'";
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
		if( $orderField=='by_customer' ){
			$order_by .=" customer_id $orderDirection";
		}else if($orderField=='by_plandate'){
			$order_by .=" plan_date $orderDirection";
		}else if($orderField=='by_balance'){
			$order_by .=" balace $orderDirection";
		}else{
			$order_by .=" plan_id desc";
		}			
		$countSql    = "select plan_id from fin_rece_plan where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select * from fin_rece_plan  where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		
		$moneySql    = "select sum(money) as sum_money from fin_rece_plan where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
				$list[$key]['customer']	=$this->customer->cst_customer_get_one($row['customer_id']);
				$list[$key]['contract']	=$this->contract->sal_contract_get_one($row['contract_id']);
				$list[$key]['ifpay_arr']	=$this->fin_rece_plan_ifpay($row['ifpay']);
			}
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$moneyRs);	
		return $assignArray;
	}
	public function fin_rece_plan_json(){
		$list	 = $this->fin_rece_plan();
		echo json_encode($list);	
	}
	public function fin_rece_plan_show(){
		$smarty  = $this->setSmarty();
		$smarty->display('erp/fin_rece_plan_show.html');	
	}		
	
	public function fin_rece_plan_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer));
			$smarty->display('erp/fin_rece_plan_add.html');	
		}else{
			$customer_id=$this->_REQUEST("customer_id");
			$contract_id=$this->_REQUEST("contract_id");
			$plan_money =$this->_REQUEST("plan_money");
			$plan_date =$this->_REQUEST("plan_date");
			$stages =$this->_REQUEST("stages");
			if(empty($customer_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择客户和销售合同");
				return false;
			}
			if(empty($plan_money)){
				$this->L("Common")->ajax_json_error("计划回款款的金额不能为0");
				return false;
			}
			$into_data=array(
				'customer_id'=>$customer_id,
				'customer_name'=>$this->_REQUEST("customer_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$plan_money,
				'plan_date'=>$plan_date,
				'stages'=>$stages,
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('fin_rece_plan',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}	
		}
	}		
	//修改付款
	public function fin_rece_plan_modify(){
		$plan_id = $this->_REQUEST("plan_id");
		if(empty($_POST)){
			$sql = "select * from fin_rece_plan where plan_id='$plan_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			$customer=$this->customer->cst_customer_list();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"bank"=>$bank));
			$smarty->display('erp/fin_rece_plan_modify.html');	
		}else{
			$customer_id=$this->_REQUEST("customer_id");
			$contract_id=$this->_REQUEST("contract_id");
			$plan_money =$this->_REQUEST("plan_money");
			$plan_date =$this->_REQUEST("plan_date");
			$stages =$this->_REQUEST("stages");
			if(empty($customer_id) || empty($contract_id)){
				$this->L("Common")->ajax_json_error("选择客户和销售合同");
				return false;
			}
			if(empty($plan_money)){
				$this->L("Common")->ajax_json_error("计划回款款的金额不能为0");
				return false;
			}
			$into_data=array(
				'customer_id'=>$customer_id,
				'customer_name'=>$this->_REQUEST("customer_name"),
				'contract_name'=>$this->_REQUEST("contract_name"),
				'contract_id'=>$contract_id,
				'money'=>$plan_money,
				'plan_date'=>$plan_date,
				'stages'=>$stages
			);
			$this->C($this->cacheDir)->modify('fin_rece_plan',$into_data,"plan_id='$plan_id'");
			$this->L("Common")->ajax_json_success("操作成功");				
		}
	}	
	//确认回款
	public function fin_rece_plan_sure(){
		$plan_id = $this->_REQUEST("plan_id");
		if(empty($_POST)){
			$sql = "select * from fin_rece_plan where plan_id='$plan_id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			$customer=$this->customer->cst_customer_list();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"bank"=>$bank));
			$smarty->display('erp/fin_rece_plan_sure.html');	
		}else{
			$this->C($this->cacheDir)->modify('fin_rece_plan',array('ifpay'=>1),"plan_id='$plan_id'");				
		}
	}	
	public function fin_rece_plan_del(){
		$plan_id=$this->_REQUEST("plan_id");
		$sql="delete from fin_rece_plan where plan_id in($plan_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	//合同状态
	public function fin_rece_plan_ifpay($key=null){
		$data=array(
			"-1"=>array(
				 		'status_name'=>'未回款',
				 		'status_name_html'=>'<span class="label label-warning">未回款<span>',
						'status_operation' => array(
                   '0' => array(
                        'act' => 'backmoney',
                        'color' => '#7266BA',
                        'name' => '回款'
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
				 		'status_name'=>'已回款',
				 		'status_name_html'=>'<span class="label label-success">已回款<span>',
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
}//
?>