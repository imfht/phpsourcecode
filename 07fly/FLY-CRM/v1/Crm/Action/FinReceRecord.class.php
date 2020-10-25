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
		_instance('Action/Auth');
	}	
	public function fin_rece_record($id=null){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;


		$busID		=$this->_REQUEST("busID");
		$busType	=$this->_REQUEST("busType");

		$sdt		=$this->_REQUEST("sdt");
		$edt		=$this->_REQUEST("edt");
	
		$where_str	 = " id>0 ";
		if($busID){
			$where_str  .= " and salID='$busID'";
		}
		if($busType){
			$where_str  .= " and busType='$busType'";
		}
		

		if($sdt){
			$where_str  .= " and paydate>='$sdt'";
		}

		if($edt){
			$where_str  .= " and paydate<='$edt'";
		}
		
		$countSql    = "select id from fin_rece_record where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$moneySql    = "select sum(money) as sum_money from fin_rece_record  where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_rece_record  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		//供应商
		$customer= array();
		$salorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["create_user"]	 = $this->L("User")->user_get_name($row['create_userID']);
				$list[$key]["blankaccount"]	 = $this->L("FinBankAccount")->fin_bank_accoun_get_name($row['blankID']);
				$list[$key]["business"]		 = $this->L("FinFlowLinkBusiness")->fin_flow_link_bus($row['salID'],$row['busType']);
				$customer[$row['id']] = $this->L("Customer")->customer_get_name($row['cusID']);
				$salorder[$row['id']] = $this->L("SalOrder")->sal_order_get_name($row['salID']);
			}
		}
		$assignArray = array('list'=>$list,'total_money'=>$moneyRs["sum_money"],
								'customer'=>$customer,'salorder'=>$salorder,'customer'=>$customer,'customer'=>$customer,
							'sdt'=>$sdt,'edt'=>$edt,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function fin_rece_record_show(){
			$list	 = $this->fin_rece_record();
			$smarty  = $this->setSmarty();
			$smarty->assign($list);
			$smarty->display('fin_rece_record/fin_rece_record_show.html');	
	}		
	
	//主要用于BOX局部调用
	public function fin_rece_record_show_box(){
			$list	= $this->fin_rece_record();
			$smarty = $this->setSmarty();
			$smarty->assign($list);
			$smarty->display('fin_rece_record/fin_rece_record_show_box.html');	
	}	
	
	//回款增加
	public function fin_rece_record_add(){
		if(empty($_POST)){
			$smarty  = $this->setSmarty();
			//$smarty->assign();//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_rece_record/fin_rece_record_add.html');	
		}else{
			$id			=$this->_REQUEST("id");
			$cusID		=$this->_REQUEST("cus_id");
			$blankID	=$this->_REQUEST("blank_id");
			$stages		=$this->_REQUEST("stages");
			$paydate	=$this->_REQUEST("paydate");
			$salID		=$this->_REQUEST("order_id");
			$sal_type	=$this->_REQUEST("order_type");
			$paymoney	=$this->_REQUEST("order_now_back_money");	
			$intro		=$this->_REQUEST("intro");	
			
			$sql= "insert into fin_rece_record(cusID,salID,paydate,money,stages,blankID,
												intro,adt,create_userID,busType) 
								values('$cusID','$salID','$paydate','$paymoney','$stages','$blankID',
									'$intro','".NOWTIME."','".SYS_USER_ID."','$sal_type');";
			
			$receID=$this->C($this->cacheDir)->update($sql);//返回收款单号
			
			if($receID>0){
		
				if($sal_type=="sal_order"){
					$this->L("SalOrder")->sal_order_pay_modify($salID,$paymoney);//更改订单回款金额
				}elseif($sal_type=="sal_contract"){
					$this->L("SalContract")->sal_contract_pay_modify($salID,$paymoney);//更改合同回款金额
				}

				$flowID=$this->L("FinFlowRecord")->fin_flow_record_add('rece',$paymoney,$blankID,$salID,$sal_type);//添加流水
				
				//$this->fin_rece_record_list_add($sal_type,$receID,$salID,$cusID,$flowID);//增加关联单号
				
				$this->L("Common")->ajax_json_success("操作成功","2","/FinReceRecord/fin_rece_record_show/");	
			}
		}
	}		
	public function fin_rece_record_del(){
		$id=$this->_REQUEST("ids");
		$sql="delete from fin_rece_record where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinReceRecord/fin_rece_record_show/");	
	}
	
	//回款记录添加 回款计划 to 回款记录
	public function fin_rece_plan_record_add($planID,$cusID,$salID,$blankID,$paydate,$money,$stages,$intro){
		$sql= "insert into fin_rece_record(
					planID,cusID,salID,blankID,paydate,money,stages,intro,adt,create_userID) 
				values(
					'$planID','$cusID','$salID','$blankID','$paydate','$money','$stages','$intro','".NOWTIME."','".SYS_USER_ID."');";
		if($this->C($this->cacheDir)->update($sql)>0){
			$this->L("SalOrder")->sal_order_pay_modify($cusID,$new_money=$money);
			$this->L("FinFlowRecord")->fin_flow_record_add('rece',$money,$blankID,$salID);//添加流水
			return true;
		}else{
			return false;	
		}	
	}

	//回款关联业务选择
	public function fin_rece_get_customer_business(){
		$order	=$this->L("SalOrder")->sal_order_select('pay_status');
		$contr	=$this->L("SalContract")->sal_contract_select('pay_status');
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
			$rtnArr[$key]["type"]	="sal_order";
			$key++;
		}
		foreach($contr as $row){
			$rtnArr[$key]=$row;
			$rtnArr[$key]["type"]	="sal_contract";
			$key++;
		}
		//print_r($rtnArr);
		echo json_encode($rtnArr);
		
	}
		
}//
?>