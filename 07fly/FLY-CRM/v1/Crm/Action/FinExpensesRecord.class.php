<?php
/*
 * 费用支出类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
//费用其他支出 
class FinExpensesRecord extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function fin_expenses_record(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from fin_expenses_record';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
	
		$moneySql    = 'select sum(money) as sum_money from fin_expenses_record';
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_expenses_record  order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		//供应商
		$customer= array();
		$salorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["create_user"]  = $this->L("User")->user_get_name($row['create_userID']);
				$list[$key]["blankaccount"] = $this->L("FinBankAccount")->fin_bank_accoun_get_name($row['blankID']);
			}
		}
		$assignArray = array('list'=>$list,'total_money'=>$moneyRs["sum_money"],
								'customer'=>$customer,'salorder'=>$salorder,'customer'=>$customer,'customer'=>$customer,
								"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function fin_expenses_record_show(){
			$list	 = $this->fin_expenses_record();
			$list["type"]=$this->L("FinExpensesType")->fin_expenses_type_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_expenses_record/fin_expenses_record_show.html');	
	}		

	public function fin_expenses_record_add(){
		if(empty($_POST)){
			$parentID	=$this->L("FinExpensesType")->fin_expenses_type_select_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));
			$smarty->display('fin_expenses_record/fin_expenses_record_add.html');	
		}else{
			$typeID		=$this->_REQUEST("parentID");
			$money		=$this->_REQUEST("money");
			$crt_date	=$this->_REQUEST("crt_date");
			$blankID	=$this->_REQUEST("blank_id");
			$intro		=$this->_REQUEST("intro");	
			
			$sql="insert into fin_expenses_record(typeID,blankID,crt_date,money,intro,adt,create_userID) 
								values('$typeID','$blankID','$crt_date','$money','$intro','".NOWTIME."','".SYS_USER_ID."');";
			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$this->L("FinFlowRecord")->fin_flow_record_add('pay',$money,$blankID,$rtn,'fin_expenses_record');//添加流水
				$this->L("Common")->ajax_json_success("操作成功","2","/FinExpensesRecord/fin_expenses_record_show/");	
			}
		}
	}	
	
	public function fin_expenses_record_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fin_expenses_record where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->fin_expenses_record_select_tree($one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_expenses_record/fin_expenses_record_modify.html');	
		}else{
			$sql= "update fin_expenses_record set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinExpensesRecord/fin_expenses_record_show/");			
		}
	}	
	//其实费用删除
	public function fin_expenses_record_del(){
		$id	=$this->_REQUEST("ids");
		$sql="delete from fin_expenses_record where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinExpensesRecord/fin_expenses_record_show/");	
	}	
	
	public function fin_expenses_record_arr(){
		$rtArr  =array();
		$sql	="select id,name from fin_expenses_record";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}			
}//end class for 07fly.com
?>