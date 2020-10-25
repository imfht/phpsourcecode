<?php
/*
 * 客户沟通记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstTalk extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_talk($cusID=0){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str 		   = " t.create_userID=u.id";

		$cus_name="";
		$cusID = $this->_REQUEST("cusID");
		if(!empty($cusID) ){
			$where_str .=" and t.cusID='$cusID'";
		}
		
		//**************************************************************************
		$countSql    = "select u.name as user_name ,t.* from cst_talk as t,fly_sys_user as u where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select u.name as user_name ,t.* from cst_talk as t,fly_sys_user as u
						where $where_str 
						order by t.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function cst_talk_show(){
			$assArr  		= $this->cst_talk();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_talk/cst_talk_show.html');	
	}

	
	public function cst_talk_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");
		if(empty($_POST)){
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$smarty = $this->setSmarty();
			$smarty->assign(array("cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_talk/cst_talk_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$cusID   = $this->_REQUEST("cusID");
			$name	 = $this->_REQUEST("talk_name");
			$sql     = "insert into cst_talk(name,cusID,adt,create_userID) 
								values('$name','$cusID','$dt','".SYS_USER_ID."');";										
			if($this->C($this->cacheDir)->update($sql)){
				$sql="update cst_customer set talk='$name' where id='$cusID'";
				$this->C($this->cacheDir)->update($sql);
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstTalk/cst_talk_show/");
			}	
		}
	}		
	
	
	public function cst_talk_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_talk where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer));
			$smarty->display('cst_talk/cst_talk_modify.html');	
		}else{//更新保存数据
			$cusID   = $this->_REQUEST("org_id");
			$sql= "update cst_talk set 
							name='$_POST[name]',cusID='$cusID',
							gender='$_POST[gender]',postion='$_POST[postion]',
							mobile='$_POST[mobile]',tel='$_POST[tel]',qicq='$_POST[qicq]',email='$_POST[email]',
							zipcode='$_POST[zipcode]',address='$_POST[address]',intro='$_POST[intro]'
			 		where id='$id'";
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstTalk/cst_talk_show_box/");
			}		
		}
	}
	
		
	public function cst_talk_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_talk where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstTalk/cst_talk_show/");	
	}	
	
	public function cst_talk_select(){
		$cusID  = $this->_REQUEST("cusID");
		$sql	="select id,name from cst_talk where cusID='$cusID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
		
	public function cst_talk_arr($cusID=""){
		$rtArr  =array();
		$where  =empty($cusID)?"":" where cusID='$cusID'";
		$sql	="select id,name from cst_talk $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}				
}//end class
?>