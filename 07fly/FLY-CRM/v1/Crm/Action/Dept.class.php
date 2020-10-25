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
class Dept extends Action{	
	
	private $cacheDir='';//缓存目录
	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function dept(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;

		$name = $this->_REQUEST("name");//方法名
		$where_str=" where id>0 ";
		if(!empty($name)){
			$where_str .=" and name like '%$name%'";
		}
	
		$countSql   = "select id from fly_sys_dept $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_dept $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function dept_show(){
			$list	 = $this->dept_table_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("list"=>$list));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('dept/dept_show.html');	
	}		
	public function lookup_tree_html(){
		$sql	= "select * from fly_sys_dept order by sort asc;";	
		$list	= $this->C($this->cacheDir)->findAll($sql);
		$data	= $this->L("Tree")->arrToTree($list,0);
		$look	= $this->L("Tree")->outToHtml($data);
		$smarty = $this->setSmarty();
		$smarty->assign(array("lookup_tree_html"=>$look));
		$smarty->display('dept/search.html');	
	}	
	public function dept_add(){
		if(empty($_POST)){
			$parentID	=$this->dept_select_tree("parentID");
			$smarty     = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('dept/dept_add.html');	
		}else{
			$sql= "insert into fly_sys_dept(name,tel,fax,parentID,sort,visible,intro) 
								values('$_POST[name]','$_POST[tel]','$_POST[fax]','$_POST[parentID]',
								'$_POST[sort]','$_POST[visible]','$_POST[intro]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Dept/dept_show/");		
		}
	}		
	public function dept_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_dept where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->dept_select_tree("parentID",$one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('dept/dept_modify.html');	
		}else{
			$sql= "update fly_sys_dept set name='$_POST[name]',
										    tel='$_POST[tel]',
											fax='$_POST[fax]',
											parentID='$_POST[parentID]',
											sort='$_POST[sort]',
											visible='$_POST[visible]',
											intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Dept/dept_show/");			
		}
	}	
	public function dept_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from fly_sys_dept where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Dept/dept_show/");	
	}	
	
	public function dept_select_tree($tag,$sid =""){
			$sql	 = "select * from fly_sys_dept  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$this->L("Tree")->tree($list);	
			$parentID  = "<select name=\"$tag\" >";
			$parentID .= "<option value='0' >请您选择</option>";
			$parentID .= $this->L("Tree")->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}
	public function dept_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$assArr  = $this->dept();
			$sql	 = "select * from fly_sys_dept  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return $tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td> \$sort</td> <td>\$spacer \$name</td> <td> \$tel</td> <td> \$fax</td><td> \$intro</td> </tr>", 0, '' , "");
	}
	
	public function dept_arr(){
		$rtArr  =array();
		$sql	="select id,name from fly_sys_dept";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}	
	//得到一个部门的得到下面子部门的编号
	public function dept_get_sub_dept($deptID){
		
		$sql		="select id,name,parentID from fly_sys_dept where parentID='$deptID'";
		$list		=$this->C($this->cacheDir)->findAll($sql);
		if(!empty($list)){
			$dept_ids   = $deptID.",";	
		}else{
			$dept_ids   = $deptID;
		}
		foreach($list as $key=>$row){
			$dept_ids .=$this->dept_get_sub_dept($row["id"]);
		}
		return $dept_ids;
	}	
		//左边菜单栏输出
	public function dept_get_sub_out($tree){
		echo "<br>dept_get_sub_out<br>";
		foreach($tree as $t){
			print_r($t['parentID']);
			if(!empty($t['parentID'])&&is_array($t['parentID'])){
				$rtArr[] .=  $this->dept_get_sub_dept($t['parentID']);
			}else{
				echo "最后一组";
				$rtArr[] .= $t['id'];
				
			}
		} 
		return $rtArr;
	}			
}//end class
?>