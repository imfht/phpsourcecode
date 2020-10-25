<?php
/*
 * 资料注入抽
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
//费用其他支出 
class FinCapitalRecord extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->type=_instance('Action/erp/FinExpensesType');
		$this->sys_user=_instance('Action/sysmanage/User');
		$this->bank=_instance('Action/erp/FinBankAccount');
		$this->flow=_instance('Action/erp/FinFlowRecord');
	}	
	
	public function fin_capital_record(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$type_id 	= $this->_REQUEST("type_id");
		$bank_id 	= $this->_REQUEST("bank_id");
		$intro 		= $this->_REQUEST("intro");
		$money 		= $this->_REQUEST("money");
		
		$where_str	 =" r.record_id>0 ";
		if($type_id){
			$where_str .=" and r.type_id='$type_id'";
		}
		if($bank_id){
			$where_str .=" and r.bank_id='$bank_id'";
		}
		if($money){
			$where_str .=" and r.money='$money'";
		}
		if($intro){
			$where_str .=" and r.intro like '%$intro%'";
		}	
		//到期时间
		if( !empty($happen_date) ){
			switch($happen_date){
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
			$where_str .=" and happen_date>'$date_range'";
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
		if( $orderField=='by_money' ){
			$order_by .=" money $orderDirection";
		}else if($orderField=='by_recordid'){
			$order_by .=" record_id $orderDirection";
		}else if($orderField=='by_happen'){
			$order_by .=" happen_date $orderDirection";
		}else{
			$order_by .=" r.record_id desc";
		}	
		
		$countSql   = "select r.* from fin_capital_record as r where {$where_str}";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		
		$moneySql   = "select sum(money) as total_money from fin_capital_record as r where {$where_str}";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		//分页查询
		$sql		 = "select r.* from fin_capital_record as r where $where_str $order_by limit $beginRecord,$pageSize ";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);

		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["bank_arr"]	  	= $this->bank->fin_bank_accoun_get_one($row['bank_id']);
				$list[$key]["type_arr"]	  	= $this->fin_capital_type($row['type_id']);
				$list[$key]['create_user_arr']	=$this->sys_user->user_get_one($row['create_user_id']);
			}
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"countMoney"=>$moneyRs);	
		return $assignArray;
	}
	public function fin_capital_record_json(){
		$list	 = $this->fin_capital_record();
		echo json_encode($list);	
	}
	public function fin_capital_record_show(){
		$bank=$this->bank->fin_bank_accoun_select();
		$smarty  = $this->setSmarty();
		$smarty->assign(array("bank"=>$bank));
		$smarty->display('erp/fin_capital_record_show.html');	
	}		

	public function fin_capital_record_add(){
		if(empty($_POST)){
			$type=$this->fin_capital_type();
			$bank=$this->bank->fin_bank_accoun_select();
			$smarty = $this->setSmarty();
			$smarty->assign(array("type"=>$type,"bank"=>$bank));
			$smarty->display('erp/fin_capital_record_add.html');	
		}else{
			$bank_id=$this->_REQUEST("bank_id");
			$type_id=$this->_REQUEST("type_id");
			$money=$this->_REQUEST("money");
			
			if(empty($money)){
				$this->L("Common")->ajax_json_error("输入的金额不能为0");
				return false;
			}
			if(empty($type_id)){
				$this->L("Common")->ajax_json_error("请选择操作类型");	
				return false;
			}
			if(empty($bank_id)){
				$this->L("Common")->ajax_json_error("请选择银行帐户");	
				return false;
			}
			
			
			$into_data=array(
				'type_id'=>$this->_REQUEST("type_id"),
				'money'=>$this->_REQUEST("money"),
				'bank_id'=>$this->_REQUEST("bank_id"),
				'happen_date'=>$this->_REQUEST("happen_date"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);

			//插入记录
			$record_id=$this->C($this->cacheDir)->insert('fin_capital_record',$into_data);
			if($type_id=='1'){
				//增加财务流水
				$this->flow->fin_flow_record_add('rece',$money,$bank_id,$record_id,'fin_capital_record');
			}else if($type_id=='-1'){
				//增加财务流水
				$this->flow->fin_flow_record_add('pay',$money,$bank_id,$record_id,'fin_capital_record');
			}
			$this->L("Common")->ajax_json_success("操作成功");	
		}
	}	

	//删除付款记录
	public function fin_capital_record_del(){
		$record_id=$this->_REQUEST("record_id");
		$record_arr=explode(",",$record_id);
		foreach($record_arr as $id){
			$sql ="select * from fin_capital_record where record_id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);
			$bank_id=$one['bank_id'];
			$money=$one['money'];
			$type_id=$one['type_id'];
			
			//删除记录
			$this->C($this->cacheDir)->delete('fin_capital_record',"record_id='$id'");
			
			//增加流水
			if($type_id=='1'){
				//增加财务流水
				$this->flow->fin_flow_record_add('pay',$money,$bank_id,$record_id,'fin_capital_record');
			}else if($type_id=='-1'){
				//增加财务流水
				$this->flow->fin_flow_record_add('rece',$money,$bank_id,$record_id,'fin_capital_record');
			}
		}
		$this->L("Common")->ajax_json_success("操作成功");	
	}

	//资料费用类型
	public function fin_capital_type($key=null){
		$data=array(
			"-1"=>array(
				 		'name'=>'资金抽取',
						'color'=>'#7266BA',
				 		'name_html'=>'<span class="label label-info">资金抽取<span>',
					),
			"1"=>array(
				 		'name'=>'资金注入',
						'color'=>'#FAD733',
				 		'name_html'=>'<span class="label label-info">资金注入<span>',
					)
		);
		return ($key)?$data[$key]:$data;
	}
	
	
	
	
}//end class for 07fly.com
?>