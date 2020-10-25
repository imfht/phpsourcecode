<?php
/*
 * 项目报备类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstFiling extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_filing(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	   	   = $this->_REQUEST("cus_name");
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str = " s.cusID=c.id ";

		if( !empty($searchValue) ){
			$where_str .=" and s.$searchKeyword like '%$searchValue%'";
		}
		if( !empty($cus_name) ){
			$where_str .=" and c.name like '%$cus_name%'";
		}		
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**************************************************************************
		$countSql    = "select c.name as cst_name ,s.* from cst_filing as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,s.* from cst_filing as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$operate[$row["id"]]=$this->cst_filing_operate($row["status"],$row["id"]);
		}
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage,"operate"=>$operate
						);	
		return $assignArray;
		
	}
	
	public function cst_filing_show(){
			$assArr  			= $this->cst_filing();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_filing_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_filing/cst_filing_show.html');	
	}

	public function cst_filing_show_box(){
			$assArr  			= $this->cst_filing();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_filing_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_filing/cst_filing_show_box.html');	
	}		
	
	public function cst_filing_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('cst_filing/cst_filing_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$cusID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$applicant_userID  = $this->_REQUEST("our_id");
			
			$sql       	= "insert into cst_filing(
									cusID,linkmanID,chanceID,applicant_userID,title,intro,support,adt,create_userID) 
							values('$cusID','$linkmanID','$chanceID','$applicant_userID',
								'$_POST[title]','$_POST[intro]','$_POST[support]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	
	public function cst_filing_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_filing where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users	 	= $this->L("User")->user_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance,"users"=>$users));
			$smarty->display('cst_filing/cst_filing_modify.html');	
		}else{//更新保存数据
			$cusID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$applicant_userID  = $this->_REQUEST("our_id");
			$sql= "update cst_filing set cusID='$cusID',linkmanID='$linkmanID',applicant_userID='$applicant_userID',chanceID='$chanceID',
							title='$_POST[title]',support='$_POST[support]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function cst_filing_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_filing where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstFiling/cst_filing_show/");	
	}
		
	public function cst_filing_audit(){
		$dt	      = date("Y-m-d H:i:s",time());
		$id	  	  = $this->_REQUEST("id");
		$status	  = $this->_REQUEST("status");
		$sql= "update cst_filing set status='$status',audit_userID='".SYS_USER_ID."',audit_dt='$dt' where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstFiling/cst_filing_show/");			
	}		
		
	public function cst_filing_status(){
		return  array("1"=>"未审核","2"=>"同意","3"=>"否决");
	}
	public function cst_filing_operate($status,$id){
		switch($status){
			case 1:
				$str="<a href='".ACT."/CstFiling/cst_filing_audit/status/2/id/$id' target='ajaxTodo' title='确定要同意吗?'>同意</a>
					  <a href='".ACT."/CstFiling/cst_filing_audit/status/3/id/$id' target='ajaxTodo' title='确定要同意吗?'>拒决</a>";
				break;
			case 2:
				$str="<a href='".ACT."/CstFiling/cst_filing_audit/status/1/id/$id' target='ajaxTodo' title='确定要撤销审核吗?'>撤销审核</a>";
				break;		
			case 3:
				$str="<a href='".ACT."/CstFiling/cst_filing_audit/status/1/id/$id' target='ajaxTodo' title='确定要撤销审核吗?'>撤销审核</a>";
				break;				
		}
		return $str;
	}
			
}//end class
?>