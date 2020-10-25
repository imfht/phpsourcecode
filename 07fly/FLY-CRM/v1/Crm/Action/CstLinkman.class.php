<?php
/*
 * 客户联系人类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstLinkman extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_linkman($cusID=0){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str 		   = " l.cusID=s.id and l.create_userID in (".SYS_USER_VIEW.")";

		if( !empty($searchValue) ){
			$where_str .=" and l.$searchKeyword like '%$searchValue%'";
		}
		
		$cus_name	=$this->_REQUEST("cus_name");
		$name		=$this->_REQUEST("name");
		$cusID 		=$this->_REQUEST("cusID");
		if(!empty($cusID) ){
			$where_str .=" and l.cusID='$cusID'";
		}
		if(!empty($name) ){
			$where_str .=" and l.name like '%$name%'";
		}
		
		//**************************************************************************
		$countSql   = "select s.name as cst_name ,l.* from cst_linkman as l,cst_customer as s where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select s.name as cst_name ,l.* from cst_linkman as l,cst_customer as s
						where $where_str 
						order by l.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function cst_linkman_show(){
			$assArr  		= $this->cst_linkman();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_linkman/cst_linkman_show.html');	
	}

	//box在显示
	public function cst_linkman_show_box(){
			$assArr  		= $this->cst_linkman();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_linkman/cst_linkman_show_box.html');	
	}		
	
	public function cst_linkman_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");
		if(empty($_POST)){
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$smarty = $this->setSmarty();
			$smarty->assign(array("cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_linkman/cst_linkman_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$cusID   = $this->_REQUEST("org_id");
			$sql     = "insert into cst_linkman(name,cusID,gender,postion,
												mobile,tel,qicq,email,zipcode,
												address,intro,adt,create_userID) 
								values('$_POST[name]','$cusID','$_POST[gender]','$_POST[postion]',
										'$_POST[mobile]','$_POST[tel]','$_POST[qicq]','$_POST[email]','$_POST[zipcode]',
										'$_POST[address]','$_POST[intro]','$dt','".SYS_USER_ID."');";										
			if($this->C($this->cacheDir)->update($sql)){
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstLinkman/cst_linkman_show/");
			}	
		}
	}		
	
	
	public function cst_linkman_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_linkman where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer));
			$smarty->display('cst_linkman/cst_linkman_modify.html');	
		}else{//更新保存数据
			$cusID   = $this->_REQUEST("org_id");
			$sql= "update cst_linkman set 
							name='$_POST[name]',cusID='$cusID',
							gender='$_POST[gender]',postion='$_POST[postion]',
							mobile='$_POST[mobile]',tel='$_POST[tel]',qicq='$_POST[qicq]',email='$_POST[email]',
							zipcode='$_POST[zipcode]',address='$_POST[address]',intro='$_POST[intro]'
			 		where id='$id'";
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstLinkman/cst_linkman_show_box/");
			}		
		}
	}
	
		
	public function cst_linkman_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_linkman where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstLinkman/cst_linkman_show/");	
	}	
	
	public function cst_linkman_select(){
		$cusID  = $this->_REQUEST("cusID");
		$sql	="select id,name from cst_linkman where cusID='$cusID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	public function cst_linkman_opt($cusID,$inputdiv,$optvalue=null){
		$where 	=empty($cusID)?"":"where cusID='$cusID'";
		$sql	="select id,name from cst_linkman $where order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$opthtml="<select name='$inputdiv'>";
		foreach($list as $row){
        $opthtml .="<option value='".$row['id']."'>".$row['name']."</option>";
		}
		$opthtml .='</select>';
		return $opthtml;
	}
		
	public function cst_linkman_arr($cusID=""){
		$rtArr  =array();
		$where  =empty($cusID)?"":" where cusID='$cusID'";
		$sql	="select id,name from cst_linkman $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}
	//返回联系人名称
	public function cst_linkman_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from cst_linkman where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
	
}//end class
?>