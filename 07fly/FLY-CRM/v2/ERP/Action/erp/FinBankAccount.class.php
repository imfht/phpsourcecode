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
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		$countSql    = "select bank_id from fin_bank_account";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		$sql		 = "select * from fin_bank_account order by sort asc, bank_id desc limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
	}
	public function fin_bank_account_json(){
		$assArr  = $this->fin_bank_account();
		echo json_encode($assArr);
	}	
	public function fin_bank_account_show(){
		$assArr  = $this->fin_bank_account();
		$smarty  = $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('erp/fin_bank_account_show.html');	
	}
	public function fin_bank_account_add(){
		if(empty($_POST)){
			$smarty     = $this->setSmarty();
			$smarty->display('erp/fin_bank_account_add.html');	
		}else{
			$into_data=array(
						'name'=>$this->_REQUEST("name"),
						'card'=>$this->_REQUEST("card"),
						'holders'=>$this->_REQUEST("holders"),
						'address'=>$this->_REQUEST("address"),
						'sort'=>$this->_REQUEST("sort"),
						'visible'=>$this->_REQUEST("visible"),
					);
			$this->C($this->cacheDir)->insert('fin_bank_account',$into_data);
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	public function fin_bank_account_modify(){
		$bank_id	  = $this->_REQUEST("bank_id");
		if(empty($_POST)){
			$sql 		= "select * from fin_bank_account where bank_id='$bank_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('erp/fin_bank_account_modify.html');	
		}else{
			$sql= "update fin_bank_account set name='$_POST[name]',
												card='$_POST[card]',
												holders='$_POST[holders]',
												address='$_POST[address]',
											   sort='$_POST[sort]',
											   visible='$_POST[visible]'
				   where bank_id='$bank_id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}	
	public function fin_bank_account_del(){
		$bank_id = $this->_REQUEST("bank_id");
		$sql  = "delete from fin_bank_account where bank_id in($bank_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	

	//下拉选择回放数据
	public function fin_bank_accoun_select(){
		$sql	="select * from fin_bank_account;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
	//传入ID卡号
	public function fin_bank_accoun_get_one($bank_id){
		if(empty($bank_id)) $bank_id=0;
		$sql	="select * from fin_bank_account where bank_id in ($bank_id)";
		$one	=$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	//排序
	public function fin_bank_account_modify_sort(){
		$bank_id = $this->_REQUEST("bank_id");
		$sort = $this->_REQUEST("sort");
		$upt_data=array(
			'sort'=>$sort,
		);
		$this->C($this->cacheDir)->modify('fin_bank_account',$upt_data,"bank_id='$bank_id'");
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	//开关
	public function fin_bank_account_modify_visible(){
		$bank_id = $this->_REQUEST("bank_id");
		$visible = $this->_REQUEST("visible");
		$upt_data=array(
			'visible'=>$visible,
		);
		$this->C($this->cacheDir)->modify('fin_bank_account',$upt_data,"bank_id='$bank_id'");
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
}//end class
?>