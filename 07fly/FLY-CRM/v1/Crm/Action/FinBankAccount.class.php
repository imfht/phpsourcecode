<?php
/*
 * 银行帐号管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinBankAccount extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}	
	
	public function fin_bank_account(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$type		 = $this->_REQUEST("type");
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = "select id from fin_bank_account";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_bank_account order by sort asc, id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,"type"=>$type);	
		return $assignArray;
	}
	public function fin_bank_account_show(){
			$assArr  = $this->fin_bank_account();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('fin_bank_account/fin_bank_account_show.html');	
	}		
	public function search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('fin_bank_account/search.html');	
	}	
	public function fin_bank_account_add(){
		$type = $this->_REQUEST("type");
		if(empty($_POST)){
			$smarty     = $this->setSmarty();
			$smarty->assign(array("type"=>$type));
			$smarty->display('fin_bank_account/fin_bank_account_add.html');	
		}else{
			$type = $this->_REQUEST("type");
			$sql  = "insert into fin_bank_account(name,card,holders,address,sort,visible) 
								values('$_POST[name]','$_POST[card]','$_POST[holders]','$_POST[address]','$_POST[sort]','$_POST[visible]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinBankAccount/fin_bank_account_show/");		
		}
	}		
	public function fin_bank_account_modify(){
		$id	  = $this->_REQUEST("id");
		$type = $this->_REQUEST("type");
		if(empty($_POST)){
			$sql 		= "select * from fin_bank_account where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"type"=>$type));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_bank_account/fin_bank_account_modify.html');	
		}else{
			$sql= "update fin_bank_account set name='$_POST[name]',
												card='$_POST[card]',
												holders='$_POST[holders]',
												address='$_POST[address]',
											   sort='$_POST[sort]',
											   visible='$_POST[visible]'
				   where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinBankAccount/fin_bank_account_show/");			
		}
	}	
	public function fin_bank_account_del(){
		$id	  = $this->_REQUEST("id");
		$type = $this->_REQUEST("type");
		$sql  = "delete from fin_bank_account where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinBankAccount/fin_bank_account_show/");	
	}	

	//下拉选择回放数据
	public function fin_bank_accoun_select(){
		$supID  = $this->_REQUEST("supID");
		$sql	= "select id,card from fin_bank_account;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	
	//传入ID卡号
	public function fin_bank_accoun_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name,card from fin_bank_account where id in ($id)";
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"].":&nbsp;".$row["card"]."";
			}
		}
		return $str;
	}	
}//end class
?>