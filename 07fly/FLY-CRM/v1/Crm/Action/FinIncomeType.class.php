<?php
/*
 * 其它费用收入分类管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinIncomeType extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function fin_income_type(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from fin_income_type';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_income_type  order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function fin_income_type_show(){
			$list	 = $this->fin_income_type_table_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("list"=>$list));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_income_type/fin_income_type_show.html');	
	}		
	public function lookup_tree_html(){
		$sql	="select * from fin_income_type  order by sort asc;";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$data	=$this->arrToTree($list,0);
		$type	=$this->outToHtml($data);
		$smarty  = $this->setSmarty();
		$smarty->assign(array("type"=>$type));//框架变量注入同样适用于smarty的assign方法
		$smarty->display('fin_income_type/search.html');	
	}	
	public function fin_income_type_add(){
		if(empty($_POST)){
			$parentID	=$this->fin_income_type_select_tree();
			$smarty     = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_income_type/fin_income_type_add.html');	
		}else{
			$sql= "insert into fin_income_type(name,parentID,sort,visible,intro) 
								values('$_POST[name]','$_POST[parentID]','$_POST[sort]','$_POST[visible]','$_POST[intro]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinIncomeType/fin_income_type_show/");		
		}
	}		
	public function fin_income_type_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fin_income_type where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->fin_income_type_select_tree($one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_income_type/fin_income_type_modify.html');	
		}else{
			$sql= "update fin_income_type set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinIncomeType/fin_income_type_show/");			
		}
	}	
	public function fin_income_type_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fin_income_type where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinIncomeType/fin_income_type_show/");	
	}	
	
	public function fin_income_type_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$assArr  = $this->fin_income_type();
			$sql	 = "select * from fin_income_type  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return $tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td> \$sort</td> <td>\$spacer \$name</td> <td> \$intro</td> </tr>", 0, '' , "");
	}	
	public function fin_income_type_select_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fin_income_type  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"parentID\" >";
			$parentID .= "<option value='0' >添加一级分类</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}
	//将数组转化为树形数组
	public function arrToTree($data,$pid){
		$tree = array();
		foreach($data as $k => $v){
			if($v['parentID'] == $pid){
				$v['parentID'] = $this->arrToTree($data,$v['id']);
				$tree[] = $v;
			}
		}        
		return $tree;
	}
	//左边菜单栏输出
	public function outToHtml($tree){
		$html = '';
		foreach($tree as $t){
			if(empty($t['parentID'])){
				$html .= "<li><a href=\"javascript:\" onclick=\"$.bringBack({typeID:'$t[id]',typeName:'$t[name]'})\">$t[name]</a></li>";
			}else{
				$html .='<li><a href="javascript:">'.$t['name'].'</a><ul>';
				$html .= $this->outToHtml($t['parentID']);
				$html  = $html.'</ul></li>';
			}
		} 
		return $html;
	}
	public function fin_income_type_arr(){
		$rtArr  =array();
		$sql	="select id,name from fin_income_type";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}			
}//
?>