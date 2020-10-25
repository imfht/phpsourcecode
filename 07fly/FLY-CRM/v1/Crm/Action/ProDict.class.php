<?php
/*
 * 产品字典类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class ProDict extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function pro_dict(){
		$currentPage	= $this->_REQUEST("pageNum");//第几页
		$numPerPage		= $this->_REQUEST("numPerPage");//每页多少条
		$currentPage	= empty($currentPage)?1:$currentPage;
		$numPerPage		= empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$type		= $this->_REQUEST("type");
		$name		= $this->_REQUEST("name");
		$where_str 	= " where type='$type' ";	
		if( !empty($name) ){
			$where_str .=" and name like '%$name%'";
		}
		
		$countSql    = "select id from pro_dict $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from pro_dict $where_str order by sort asc, id desc limit $beginRecord,$numPerPage";	
		
		
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,"type"=>$type);	
		return $assignArray;
	}
	public function pro_dict_show(){
			$assArr  = $this->pro_dict();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('pro_dict/pro_dict_show.html');	
	}		
	public function search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('pro_dict/search.html');	
	}	
	public function pro_dict_add(){
		$type = $this->_REQUEST("type");
		if(empty($_POST)){
			$smarty     = $this->setSmarty();
			$smarty->assign(array("type"=>$type));
			$smarty->display('pro_dict/pro_dict_add.html');	
		}else{
			$type = $this->_REQUEST("type");
			$sql  = "insert into pro_dict(name,type,sort,visible) 
								values('$_POST[name]','$type','$_POST[sort]','$_POST[visible]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/ProDict/pro_dict_show/type/$type/");		
		}
	}		
	public function pro_dict_modify(){
		$id	  = $this->_REQUEST("id");
		$type = $this->_REQUEST("type");
		if(empty($_POST)){
			$sql 		= "select * from pro_dict where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"type"=>$type));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('pro_dict/pro_dict_modify.html');	
		}else{
			$sql= "update pro_dict set name='$_POST[name]',sort='$_POST[sort]',visible='$_POST[visible]' where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/ProDict/pro_dict_show/type/$type/");			
		}
	}	
	public function pro_dict_del(){
		$id	  = $this->_REQUEST("id");
		$type = $this->_REQUEST("type");
		$sql  = "delete from pro_dict where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/ProDict/pro_dict_show/type/$type/");	
	}			
}//end class
?>