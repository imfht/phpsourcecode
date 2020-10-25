<?php
/*
 * 权限方法管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Method extends Action{	
	
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function method(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		$name = $this->_REQUEST("name");//方法名
		$where_str=" where id>0 ";
		if(!empty($name)){
			$where_str .=" and value='$name'";
		}
		
		$countSql   = "select id from fly_sys_method $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_method $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function method_show(){
			$assArr  = $this->method();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('method/method_show.html');	
	}		
	
	public function method_add(){
		if(empty($_POST)){
			$menuID		=$this->L("Menu")->menu_select_tree("menuID");
			$smarty     = $this->setSmarty();
			$smarty->assign(array("menuID"=>$menuID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('method/method_add.html');	
		}else{
			$sql= "insert into fly_sys_method(name,value,menuID,sort,visible) 
								values('$_POST[name]','$_POST[value]','$_POST[menuID]','$_POST[sort]','$_POST[visible]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Method/method_show/");		
		}
	}		
	public function method_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_method where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$menuID		=$this->L("Menu")->menu_select_tree("menuID",$one["menuID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"menuID"=>$menuID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('method/method_modify.html');	
		}else{
			$sql= "update fly_sys_method set name='$_POST[name]',value='$_POST[value]',menuID='$_POST[menuID]',sort='$_POST[sort]',visible='$_POST[visible]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Method/method_show/");			
		}
	}	
	public function method_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_method where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Method/method_show/");	
	}	
	
	public function method_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_method  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return	$tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td>\$id</td> <td>\$spacer \$name</td> <td> \$url</td> </tr>", 0, '' , "");
	}
	public function method_select_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_method  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"parentID\" >";
			$parentID .= "<option value='0' >添加一级分类</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}
	//转换在以栏目ID为关键字的二维数组
	public function method_arr(){
		$rtArr  =array();
		$sql	="select id,menuID,name,value from fly_sys_method order by sort asc,id desc ";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["menuID"]][$row["value"]]=$row["name"];
			}
		}
		return $rtArr;
	}	
	//转换在ID数组
	public function method_auth_arr(){
		$rtArr  = array();
		$sql	= "select value from fly_sys_method order by sort asc,id desc ";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$v){
			$rtArr[]=$v["value"];
		}
		return $rtArr;
	}		
			
}//
?>