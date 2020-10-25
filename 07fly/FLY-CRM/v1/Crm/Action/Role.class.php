<?php
/*
 * 权限管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Role extends Action{	

	private $cacheDir='';//缓存目录
	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function role(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		//查询条件
		$name = $this->_REQUEST("name");
		$where_str=" where id>0 ";
		if(!empty($name)){
			$where_str .=" and name like '%$name%'";
		}
		$countSql   = "select id from fly_sys_role $where_str";
		$totalCount	= $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_role $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function role_show(){
			$assArr  = $this->role();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);//框架变量注入同样适用于smarty的assign方法
			$smarty->display('role/role_show.html');	
	}		
	public function search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('role/search.html');	
	}	
	public function role_add(){
		if(empty($_POST)){
			$menu		= $this->menu_power_check();
			$smarty     = $this->setSmarty();
			$smarty->assign(array("menu"=>$menu));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('role/role_add.html');	
		}else{
			$name  		= $_POST["name"];
			$intro 		= $_POST["intro"];
			$sort 		= $_POST["sort"];
			$menuID		= is_array($_POST["menuID"])?implode(',',$_POST["menuID"]):0;
			$methodID	= is_array($_POST["methodID"])?implode(',',$_POST["methodID"]):0;
			$areaID		= is_array($_POST["areaID"])?implode(',',$_POST["areaID"]):0;
			
			
			$this->C($this->cacheDir)->begintrans();
			$sql 	= "insert into fly_sys_role(sort,name,intro) values('$sort','$name','$intro')";	
			$roleID = $this->C($this->cacheDir)->update($sql);
			if($roleID<=0){
				$this->C($this->cacheDir)->rollback();
			}
			$sqlstr1 = "insert into fly_sys_power(master,master_value,access,access_value) values('role','$roleID','SYS_MENU','$menuID');";
			$sqlstr2 = "insert into fly_sys_power(master,master_value,access,access_value) values('role','$roleID','SYS_METHOD','$methodID');";
			$sqlstr3 = "insert into fly_sys_power(master,master_value,access,access_value) values('role','$roleID','SYS_AREA','$areaID');";
			if( $this->C($this->cacheDir)->update($sqlstr1)<=0 || $this->C($this->cacheDir)->update($sqlstr2)<=0 || $this->C($this->cacheDir)->update($sqlstr3)<=0 ){
				$this->C($this->cacheDir)->rollback();
			}			
			$this->C($this->cacheDir)->commit();	

			$this->L("Common")->ajax_json_success("操作成功","1","/Role/role_show/");		
		}
	}		
	public function role_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_role where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$menu		= $this->menu_power_check($id);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"menu"=>$menu));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('role/role_modify.html');	
		}else{
			$name  		= $_POST["name"];
			$intro 		= $_POST["intro"];
			$sort 		= $_POST["sort"];
			$menuID		= is_array($_POST["menuID"])?implode(',',$_POST["menuID"]):0;
			$methodID	= is_array($_POST["methodID"])?implode(',',$_POST["methodID"]):0;
			//$areaID		= is_array($_POST["areaID"])?implode(',',$_POST["areaID"]):0;
			
			$this->C($this->cacheDir)->begintrans();
			
			$sql="update fly_sys_role set name= '$_POST[name]', intro= '$_POST[intro]' where id='$id'";	
			if($this->C($this->cacheDir)->update($sql)<0){
				$this->C($this->cacheDir)->rollback();
			}	
			$sqlstr1 = "update  fly_sys_power set  access_value='$menuID' where master='role' and access='SYS_MENU' and master_value='$id'";
			$sqlstr2 = "update  fly_sys_power set  access_value='$methodID' where master='role' and access='SYS_METHOD' and master_value='$id'";
			//$sqlstr3 = "update  fly_sys_power set  access_value='$areaID' where master='role' and access='SYS_AREA' and master_value='$id'";
			
/*			echo $sqlstr1."<hr>";
			echo $sqlstr2."<hr>";
			echo $sqlstr3."<hr>";*/
			if($this->C($this->cacheDir)->update($sqlstr1)<0 || $this->C($this->cacheDir)->update($sqlstr2)<0){
				$this->C($this->cacheDir)->rollback();
			}
			$this->C($this->cacheDir)->commit();
			$this->L("Common")->ajax_json_success("操作成功","1","/Role/role_show/");			
		}
	}	
	
	public function role_del(){
		$id=$this->_REQUEST("id");
		$sqlstr1 = "delete from fly_sys_role where id in ($id)";	
		$sqlstr2 = "delete from fly_sys_power where master_value in ($id) and master='role'";										
		$this->C($this->cacheDir)->begintrans();
		if($this->C($this->cacheDir)->update($sqlstr1)<0 || $this->C($this->cacheDir)->update($sqlstr2)<0 ){
			$this->C($this->cacheDir)->rollback();
		}
		$this->C($this->cacheDir)->commit();
		$this->L("Common")->ajax_json_success("操作成功","1","/Role/role_show/");	
	}	
	

	//系统栏目和权限列表
	public function menu_power_check($id=null){
		$list 	   = $this->L("Menu")->menu_tree_arr();
		$method	   = $this->L("Method")->method_arr();
		$role_menu = array();
		$role_mod  = array();
		if($id){
			$result = $this->role_get_one($id);
			$role_menu =explode(',',$result["SYS_MENU"]); 
			$role_mod  =explode(',',$result["SYS_METHOD"]); 
		}
		$string  = "<table width=\"100%\"  border='0' cellpadding='5' cellspacing='0' layoutH=\"138\">";
		$string .= "<tr bgcolor='#FBF5C6' height='25'><td>栏目</td><td>菜单</td></tr>";
		$cnt	 = 1;
		if(is_array($list)){
			foreach($list as  $key=>$row1){
				$ischeck1=in_array($row1["id"],$role_menu)?"checked":"";
				$string .="<tr><td width='10%'>".$row1["name"]."<input type='checkbox' name='menuID[]' value='".$row1["id"]."' $ischeck1 id='".$row1["id"]."' onclick='checkedStatus(this.id);'></td><td id='sub".$row1["id"]."'>" ;
					foreach($row1["parentID"] as $item_key=>$row2){
						$bgcolor =($cnt%2==0)?"#FBF5C6":"#F9F9F9";
						$ischeck2=in_array($row2["id"],$role_menu)?"checked":"";
						$string .= "<table width=\"100%\"><tr  bgcolor='$bgcolor'><td width='15%'><input type='checkbox' name='menuID[]' value='".$row2["id"]."' $ischeck2 id='".$row2["id"]."'  onclick='checkedStatus(this.id);'> ".$row2["name"]."</td><td id='sub".$row2["id"]."'>";	
							foreach($row2["parentID"] as $item_key=>$row3){
								$ischeck3=in_array($row3["id"],$role_menu)?"checked":"";
								$string .= "<table  width=\"100%\"><tr><td width='20%' height='25'><input type='checkbox' name='menuID[]' value='".$row3["id"]."' $ischeck3 id='".$row3["id"]."'  onclick='checkedStatus(this.id);'> ".$row3["name"]."</td><td id='sub".$row3["id"]."' align=left>";	
									if( @is_array($method[$row3["id"]]) ){
										$string .= "<table><tr>";
										foreach( $method[$row3["id"]] as $mkey=>$mvalue){
											$ischeck4=in_array($mkey,$role_mod)?"checked":"";
											$string .= "<td width='100' height='25'><input type='checkbox' name='methodID[]' value='$mkey' $ischeck4 > $mvalue </td>";
										}
										$string .= "</td></tr></table>";	
									}
									
								$string .= "</td></tr></table>";			
							}							
						$cnt++;	
						$string .= "</td></tr></table>";		
					}
				$string .= "</td></tr>";			
			}
			$string .= "</table>";
		}
		return $string;	
	}
	
	public function role_get_one($id){
		$sql  = "select access,access_value from fly_sys_power where master='role' and master_value='$id' ";
		$list =$this->C($this->cacheDir)->findAll($sql);//查询结果为二维数组，需foreach循环
		if(is_array($list)){
			foreach($list as $key=>$row){
				$power[$row['access']] = $row["access_value"];
			}
		}	
		return $power;	
	}
	
	public function role_select_tree($tag,$sid =""){
			$sql	 = "select * from fly_sys_role  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$this->L("Tree")->tree($list);	
			$parentID  = "<select name=\"$tag\" >";
			$parentID .= "<option value='0' >请您选择</option>";
			$parentID .= $this->L("Tree")->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}	
	
	public function role_arr(){
		$rtArr  =array();
		$sql	="select id,name from fly_sys_role";
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