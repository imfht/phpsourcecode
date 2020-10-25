<?php	 
class Region extends Action{	
	
	var $common;
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function region(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$countSql    = 'select id from region';
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_region  order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function region_show(){
			$list	 = $this->region_table_tree();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("list"=>$list));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('region/region_show.html');	
	}		
	public function lookup_tree_html(){
		$sql	="select * from fly_sys_region  order by id asc;";	
		$list	=$this->C($this->cacheDir)->findAll($sql);
		$data	=$this->arrToTree($list,1);
		$type	=$this->outToHtml($data);
		$smarty  = $this->setSmarty();
		$smarty->assign(array("type"=>$type));//框架变量注入同样适用于smarty的assign方法
		$smarty->display('region/search.html');	
	}	
	public function region_add(){
		if(empty($_POST)){
			$parentID	=$this->region_select_tree();
			$smarty     = $this->setSmarty();
			$smarty->assign(array("parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('region/region_add.html');	
		}else{
			$sql= "insert into fly_sys_region(name,parentID,sort,visible,intro) 
								values('$_POST[name]','$_POST[parentID]','$_POST[sort]','$_POST[visible]','$_POST[intro]');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Region/region_show/");		
		}
	}		
	public function region_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from region where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->region_select_tree($one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('region/region_modify.html');	
		}else{
			$sql= "update region set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Region/region_show/");			
		}
	}	
	public function region_del(){
		$id=$this->_REQUEST("id");
		$sql="delete from region where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Region/region_show/");	
	}	
	
	public function region_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$assArr  = $this->region();
			$sql	 = "select * from region  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return $tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td> \$sort</td> <td>\$spacer \$name</td> <td> \$intro</td> </tr>", 0, '' , "");
	}	
	public function region_select_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from region  order by sort asc;";	
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
				$html .= "<li><a href=\"javascript:\" onclick=\"$.bringBack({region:'$t[id]',regionName:'$t[name]'})\">$t[name]</a></li>";
			}else{
				$html .='<li><a href="javascript:">'.$t['name'].'</a><ul>';
				$html .= $this->outToHtml($t['parentID']);
				$html  = $html.'</ul></li>';
			}
		} 
		return $html;
	}
			
}//
?>