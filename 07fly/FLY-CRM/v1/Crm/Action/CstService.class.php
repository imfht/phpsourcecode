<?php
/*
 * 客户服务记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstService extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_service(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	   	   = $this->_REQUEST("cus_name");
		$status 	   	   = $this->_REQUEST("status");
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$cusID			   = $this->_REQUEST("cusID");
		$where_str = " s.cusID=c.id and s.create_userID in (".SYS_USER_VIEW.")";

		if( !empty($searchValue) ){
			$where_str .=" and s.$searchKeyword like '%$searchValue%'";
		}
		if( !empty($cus_name) ){
			$where_str .=" and c.name like '%$cus_name%'";
		}		
		if( !empty($status) ){
			$where_str .=" and s.status='$status'";
		}	
		$cus_name="";
		if( !empty($cusID) ){
			$where_str .=" and s.cusID='$cusID'";
			$cus_name	=$this->L("Customer")->customer_get_name($cusID);
		}		
		
		//**************************************************************************
		$countSql    = "select c.name as cst_name ,s.* from cst_service as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,s.* from cst_service as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function cst_service_show(){
			$assArr  		= $this->cst_service();
			$assArr["dict"] = $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] = $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] = $this->cst_service_status();
			$assArr["statusselect"] = $this->cst_service_status_select("status");
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_service/cst_service_show.html');	
	}		

	//box在显示
	public function cst_service_show_box(){
			$assArr  				= $this->cst_service();
			$assArr["dict"] 		= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 		= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"]		= $this->cst_service_status();
			$assArr["statusselect"] = $this->cst_service_status_select("status");
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_service/cst_service_show_box.html');	
	}

	public function cst_service_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");
		if(empty($_POST)){
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$status = $this->cst_service_status_select("status");
			$smarty = $this->setSmarty();
			$smarty->assign(array("status"=>$status,"cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_service/cst_service_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$cusID   = $this->_REQUEST("org_id");
			$services   = $this->_REQUEST("services_id");
			$servicesmodel   = $this->_REQUEST("servicesmodel_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$sql     = "insert into cst_service(cusID,services,servicesmodel,linkmanID,bdt,
													tlen,status,content,intro,adt,create_userID) 
								values('$cusID','$services','$servicesmodel','$linkmanID','$_POST[bdt]',
								'$_POST[tlen]','$_POST[status]','$_POST[content]','$_POST[intro]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功",'1','/CstService/cst_service_show/');		
		}
	}		

	public function cst_service_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_service where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict	    = $this->L("CstDict")->cst_dict_arr();
			$status 	= $this->cst_service_status_select("status",$one["status"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"status"=>$status));
			$smarty->display('cst_service/cst_service_modify.html');	
		}else{//更新保存数据
		
			$cusID   	 	= $this->_REQUEST("org_id");
			$linkmanID   	= $this->_REQUEST("linkman_id");
			$services   	= $this->_REQUEST("services_id");
			$servicesmodel  = $this->_REQUEST("servicesmodel_id");
			$sql= "update cst_service set 
							cusID='$cusID',linkmanID='$linkmanID',services='$services',servicesmodel='$servicesmodel',
							bdt='$_POST[bdt]',tlen='$_POST[tlen]',
							status='$_POST[status]',content='$_POST[content]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功",'1','/CstService/cst_service_show/');			
		}
	}
	
		
	public function cst_service_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_service where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstService/cst_service_show/");	
	}	

	public function cst_service_status(){
		return  array("1"=>"<a style='color:#538DC6'>无需处理</a>",
					  "2"=>"<a style='color:#E7453D'>未处理</a>",
					  "3"=>"<a style='color:#FBD10A'>处理中</a>",
					  "4"=>"<a style='color:#4BB848'>处理完成</a>");
	}
	
	public function cst_service_status_select($inputname,$value=""){
		$data=$this->cst_service_status();
		$string ="<select name='$inputname'>";
		foreach($data as $key=>$va){
			$string.="<option value='$key'";
			if($key==$value) $string.=" selected";
			$string.=">".$va."</option>";
		}
		$string.="</select>";
		return $string;
	}		
			
}//end class
?>