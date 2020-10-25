<?php
/*
 *
 * admin.MemberAddress  会员地址管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和行业系统开发
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com> 574249366
 * @version ：1.0
 * @link ：http://www.07fly.top 
 */	
class MemberAddress extends Action{	

	private $cacheDir='';//缓存目录
	private $auth;
	private $dept;//部门
	private $postion;//职位
	private $role;//权限
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
	}	
	public function member_address(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");

		$where_str = "id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		
		if( !empty($name) ){
			$where_str .=" and name like '%$name%'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}		
		//**************************************************************************
		$countSql    = "select id from fly_member_address where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_member_address  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$mem =$this->L('admin/Member');
		foreach($list as $key=>$row){
			$list[$key]['member_name']  =$mem->member_get_name($row['member_id']);
		}		
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function member_address_show(){
			$assArr  = $this->member_address();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/member_address_show.html');	
	}		

	public function member_address_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/member_address_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$cardname  = $this->_REQUEST("cardname");
			$cardnumber  = $this->_REQUEST("cardnumber");
			$name  = $this->_REQUEST("name");
			$member_id  = $this->_REQUEST("member_id");
			$sql     = "insert into fly_member_address(cardname,cardnumber,name,member_id,adt) 
								values('$cardname','$cardnumber','$name','$member_id','$dt');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/admin/MemberAddress/member_address_show/");		
		}
	}		
	
	public function member_address_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_member_address where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/member_address_modify.html');	
		}else{//更新保存数据
			$cardname  = $this->_REQUEST("cardname");
			$cardnumber  = $this->_REQUEST("cardnumber");
			$name  = $this->_REQUEST("name");
			$member_id  = $this->_REQUEST("member_id");
			$sql= "update fly_member_address set 
							cardname='$cardname',
							cardnumber='$cardnumber',
							name='$name',
							member_id='$member_id'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/admin/MemberAddress/member_address_show/");	
		}
	}
	
	public function member_address_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from fly_member_address where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/admin/MemberAddress/member_address_show/");	
	}
	//传入ID返回名字
	public function member_address_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select name as name from fly_member_address where id in ($id) order by id desc";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $key=>$row){
				$str .= " ".$row["name"]."";
			}
		}
		return $str;
	}
	
	//传入ID返回名字
	public function member_address_get_one($id){
		if(empty($id)) $id=0;
		$sql ="select * from fly_member_address where id='$id'";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}

}//
?>