<?php
/*
 * 供应商联系人类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class SupLinkman extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function sup_linkman(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = " l.supID=s.id ";

		if( !empty($searchValue) ){
			$where_str .=" and l.$searchKeyword like '%$searchValue%'";
		}
		//**************************************************************************
		$countSql    = "select s.name as sup_name ,l.* from sup_linkman as l,sup_supplier as s where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select s.name as sup_name ,l.* from sup_linkman as l,sup_supplier as s
						where $where_str 
						order by l.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function sup_linkman_show(){
			$assArr  		= $this->sup_linkman();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sup_linkman/sup_linkman_show.html');	
	}		
	
	public function sup_linkman_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('sup_linkman/sup_linkman_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$supID   = $this->_REQUEST("org_id");
			$sql     = "insert into sup_linkman(name,supID,gender,postion,mobile,tel,qicq,email,zipcode,address,intro,adt) 
								values('$_POST[name]','$supID',
										'$_POST[gender]','$_POST[postion]','$_POST[mobile]','$_POST[tel]',
										'$_POST[qicq]','$_POST[email]','$_POST[zipcode]','$_POST[address]','$_POST[intro]','$dt');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	
	public function sup_linkman_modify(){
		$id	  	 = $this->_REQUEST("id");
		$supID   = $this->_REQUEST("org_id");
		
		if(empty($_POST)){
			$sql 		= "select * from sup_linkman where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$supplier   = $this->L("Supplier")->supplier_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"supplier"=>$supplier));
			$smarty->display('sup_linkman/sup_linkman_modify.html');	
		}else{//更新保存数据
			$sql= "update sup_linkman set 
							name='$_POST[name]',supID='$supID',
							gender='$_POST[gender]',postion='$_POST[postion]',
							mobile='$_POST[mobile]',tel='$_POST[tel]',qicq='$_POST[qicq]',email='$_POST[email]',
							zipcode='$_POST[zipcode]',address='$_POST[address]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function sup_linkman_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from sup_linkman where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Supplier/sup_linkman_show/");	
	}	
	public function sup_linkman_select(){
		$supID  = $this->_REQUEST("supID");
		$sql	= "select id,name from sup_linkman where supID='$supID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
		
	public function sup_linkman_arr($supID=""){
		$rtArr  =array();
		$where  =empty($supID)?"":" where supID='$supID'";
		$sql	="select id,name from sup_linkman $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}	 	
			
}// end class 
?>