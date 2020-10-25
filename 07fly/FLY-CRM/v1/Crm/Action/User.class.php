<?php
/*
 * 系统用户管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class User extends Action{	

	private $cacheDir='';//缓存目录
	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function user(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询

		$name	   = $this->_REQUEST("name");
		$tel	   = $this->_REQUEST("tel");
		$linkman   = $this->_REQUEST("linkman");
		$ecotype   = $this->_REQUEST("org1_id");
		$trade     = $this->_REQUEST("org2_id");
		$fax   	   = $this->_REQUEST("fax");
		$email     = $this->_REQUEST("email");
		$address   = $this->_REQUEST("address");	
		$bdt   	   = $this->_REQUEST("bdt");
		$edt   	   = $this->_REQUEST("edt");
		$where_str = "id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		if( !empty($name) ){
			$where_str .=" and name like '%$name%'";
		}
		if( !empty($tel) ){
			$where_str .=" and tel like '%$tel%'";
		}	
		if( !empty($linkman) ){
			$where_str .=" and linkman like '%$linkman%'";
		}	
		if( !empty($ecotype) ){
			$where_str .=" and ecotype ='$ecotype'";
		}	
		if( !empty($trade) ){
			$where_str .=" and trade ='$trade'";
		}	
		if( !empty($fax) ){
			$where_str .=" and fax like '%$fax%'";
		}	
		if( !empty($email) ){
			$where_str .=" and email like '%$email%'";
		}	
		if( !empty($address) ){
			$where_str .=" and address like '%$address%'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}		
		//**************************************************************************
		$countSql    = "select id from fly_sys_user where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_sys_user  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function user_show(){
			$assArr  			= $this->user();
			$assArr["dept"] 	= $this->L("Dept")->dept_arr();
			$assArr["position"] = $this->L("Position")->position_arr();
			$assArr["role"]		= $this->L("Role")->role_arr();
			$smarty   			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('user/user_show.html');	
	}		
	public function lookup_search(){
			$assArr  			= $this->user();
			$assArr["dept"] 	= $this->L("Dept")->dept_arr();
			$assArr["position"] = $this->L("Position")->position_arr();
			$assArr["role"]		= $this->L("Role")->role_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('user/lookup_search.html');	
	}	
	
	public function advanced_search(){
		$smarty  = $this->setSmarty();
		$smarty->display('user/advanced_search.html');	
	}	
	
	public function user_add(){
		if(empty($_POST)){
			$dept 		= $this->L("Dept")->dept_select_tree("deptID");
			$position	= $this->L("Position")->position_select_tree("positionID");
			$role		= $this->L("Role")->role_select_tree("roleID");
			$smarty = $this->setSmarty();
			$smarty->assign(array("dept"=>$dept,"position"=>$position,"role"=>$role));
			$smarty->display('user/user_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$sql     = "insert into fly_sys_user(account,password,name,gender,deptID,positionID,roleID,mobile,tel,qicq,email,zipcode,address,intro,adt) 
								values('$_POST[account]','$_POST[password]','$_POST[name]','$_POST[gender]','$_POST[deptID]',
										'$_POST[positionID]','$_POST[roleID]','$_POST[mobile]','$_POST[tel]',
										'$_POST[qicq]','$_POST[email]','$_POST[zipcode]','$_POST[address]','$_POST[intro]','$dt');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	
	public function user_modify(){
		$id	  	 = $this->_REQUEST("id");
	
		if(empty($_POST)){
			$sql 		= "select * from fly_sys_user where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$dept 		= $this->L("Dept")->dept_select_tree("deptID",$one["deptID"]);
			$position	= $this->L("Position")->position_select_tree("positionID",$one["positionID"]);
			$role		= $this->L("Role")->role_select_tree("roleID",$one["roleID"]);
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"dept"=>$dept,"position"=>$position,"role"=>$role));
			$smarty->display('user/user_modify.html');	
		}else{//更新保存数据
			$sql= "update fly_sys_user set 
							name='$_POST[name]',password='$_POST[password]',gender='$_POST[gender]',deptID='$_POST[deptID]',positionID='$_POST[positionID]',
							roleID='$_POST[roleID]',mobile='$_POST[mobile]',tel='$_POST[tel]',qicq='$_POST[qicq]',email='$_POST[email]',
							zipcode='$_POST[zipcode]',address='$_POST[address]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function user_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from fly_sys_user where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/User/user_show/");	
	}	
	//返回所用系统用户数据，以数组方式
	public function user_arr(){
		$rtArr  =array();
		$sql	="select id,name from fly_sys_user";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}		

	//得到一个系统用户权限
	//return Array ( [sys_menu] => Array ( [0] => 10,101,102,105,20,30,50 [1] => 1,507 ) )
	public function user_get_power($id=1){
		$sql	= "select roleID from fly_sys_user where id='$id'";				 
		$one 	= $this->C($this->cacheDir)->findOne($sql);
		$role   = explode(",",$one["roleID"]);
		if(is_array($role)){
			foreach($role as $k=>$v){
				$power=$this->L("Role")->role_get_one($v);//多个权限叠加进去
				foreach($power as $key=>$val){
					$pArr[$key][]=$val;
				}
			}
		}
		return $pArr;	
	}	
	
	//获取同当前用户管理的用户编号
	public function user_get_sub_user($id=null){
		$sql	 = "select deptID from fly_sys_user where id='$id'";	
		$one 	 = $this->C($this->cacheDir)->findOne($sql);
		$deptStr = $this->L("Dept")->dept_get_sub_dept($one["deptID"]);//得到部门，以及管属下面部门编号 
		$deptStr = rtrim($deptStr, ',');
		//查询这个部门下所有的员工编号
		$sql	 = "select id,name,account from fly_sys_user where deptID in ($deptStr)";	
		$list 	 = $this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[]=$row["id"];
			}
		}	
		return $rtArr;	
	}	
	//传入ID返回名字
	public function user_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from fly_sys_user where id in ($id)";	
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