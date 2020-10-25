<?php
/*
 * 部门管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Position extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function position(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from fly_sys_position';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_position  order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function position_show(){
			$list	 = $this->position_table_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("list"=>$list));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('position/position_show.html');	
	}		
	public function lookup_tree_html(){
		$sql	="select * from fly_sys_position order by sort asc;";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$data	=$this->L("Tree")->arrToTree($list,0);
		$look	=$this->L("Tree")->outToHtml($data);
		$smarty  = $this->setSmarty();
		$smarty->assign(array("lookup_tree_html"=>$look));
		$smarty->display('position/search.html');	
	}	
	
	public function position_add(){
		if(empty($_POST)){
			$parentID	=$this->position_select_tree("parentID");
			$smarty     = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('position/position_add.html');	
		}else{
			$sql= "insert into fly_sys_position(name,parentID,sort,visible,intro) 
								values('$_POST[name]','$_POST[parentID]','$_POST[sort]','$_POST[visible]','$_POST[intro]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Position/position_show/");		
		}
	}		
	public function position_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_position where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->position_select_tree("parentID",$one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('position/position_modify.html');	
		}else{
			$sql= "update fly_sys_position set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Position/position_show/");			
		}
	}	
	public function position_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_position where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Position/position_show/");	
	}	
	
	public function position_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$assArr  = $this->position();
			$sql	 = "select * from fly_sys_position  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return $tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td> \$sort</td> <td>\$spacer \$name</td> <td> \$intro</td> </tr>", 0, '' , "");
			
	}
	public function position_select_tree($tag,$sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fly_sys_position  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"$tag\" >";
			$parentID .= "<option value='0' >请您选择</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}	
	public function position_arr(){
		$rtArr  =array();
		$sql	="select id,name from fly_sys_position";
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