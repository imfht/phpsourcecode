<?php
/*
 * 系统菜单管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Menu extends Action{	
	
	var $common;
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function menu(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from fly_sys_menu';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_menu  order by sort asc,id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function menu_show(){
		    $list	 = $this->menu_table_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("list"=>$list));
			$smarty->display('menu/menu_show.html');	
	}		
	public function search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('menu/search.html');	
	}	
	public function menu_add(){
		if(empty($_POST)){
			$parentID	=$this->menu_select_tree("parentID");
			$smarty     = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('menu/menu_add.html');	
		}else{
			$sql= "insert into fly_sys_menu(name,name_en,url,parentID,sort,visible) 
								values('$_POST[name]','$_POST[name_en]','$_POST[url]','$_POST[parentID]','$_POST[sort]','$_POST[visible]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Menu/menu_show/");		
		}
	}		
	public function menu_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_menu where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->menu_select_tree("parentID",$one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('menu/menu_modify.html');	
		}else{
			$sql= "update fly_sys_menu set name='$_POST[name]',name_en='$_POST[name_en]',
											url='$_POST[url]',parentID='$_POST[parentID]',sort='$_POST[sort]',visible='$_POST[visible]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Menu/menu_show/");			
		}
	}	
	public function menu_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_menu where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Menu/menu_show/");	
	}	
	
	public function menu_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_menu order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return	$tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td>\$id</td><td>\$visible</td> <td>\$spacer \$name</td> <td> \$url</td> </tr>", 0, '' , "");
	}
	public function menu_select_tree($tag,$sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_menu  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"$tag\" >";
			$parentID .= "<option value='0' >添加一级分类</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}

	public function lookup_tree_html(){
		$sql	="select * from fly_sys_menu  order by id asc;";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$data	=$this->arrToTree($list,0);
		$type	=$this->outToHtml($data);
		$smarty  = $this->setSmarty();
		$smarty->assign(array("type"=>$type));//框架变量注入同样适用于smarty的assign方法
		$smarty->display('region/search.html');	
	}
	//将数组转化为树形数组
	public function arrToTree($data,$pid){
		//echo $pid;
		$tree = array();
		foreach($data as $k => $v){
			if($v['parentID'] == $pid){
				$v['parentID'] = $this->arrToTree($data,$v['id']);
				$tree[] = $v;
				//echo "<hr>";
			}
		}   
		return $tree;
	}
	
	//左边菜单栏输出
	public function outToHtml($tree){
		$html = '';
		foreach($tree as $t){
			if(empty($t['parentID'])){
				$html .= "<li><a href=\"javascript:\" onclick=\"$.bringBack({region:'$t[id]',regionName:'$t[name]'})\">$t[name]</a></li>";
			}else{
				$html .='<li><a href="javascript:">'.$t['name'].'</a><ul>';
				$html .= $this->outToHtml($t['parentID']);
				$html  = $html.'</ul></li>';
			}
		} 
		return $html;
	}	
		
	
	public function menu_tree_arr(){
		$sql	="select * from fly_sys_menu  order by sort asc,id desc;";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$data	=$this->arrToTree($list,0);	
		return $data;
	}


	//转换在ID数组
	public function menu_auth_arr(){
		$rtArr  =array();
		$sql	="select id from fly_sys_menu order by sort asc,id desc ";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$v){
			$rtArr[]=$v["id"];
		}
		return $rtArr;
	}		
			
}//end class
?>