<?php
/*
 * 网站管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstWebsite extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function cst_website($cusID=0){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$bdt	   = $this->_REQUEST("bdt");
		$edt	   = $this->_REQUEST("edt");
		$bdt1	   = $this->_REQUEST("bdt1");
		$edt1	   = $this->_REQUEST("edt1");		
		$status	   = $this->_REQUEST("status");		
		$where_str 		   = " w.cusID=s.id and w.create_userID in (".SYS_USER_VIEW.")";

		if( !empty($searchValue) ){
			$where_str .=" and w.$searchKeyword like '%$searchValue%'";
		}
		//开始时间搜索
		if( !empty($bdt) ){
			$where_str .=" and w.bdt>'$bdt'";
		}
		if( !empty($edt) ){
			$where_str .=" and w.bdt<'$edt'";
		}	
		//到期时间
		if( !empty($bdt1) ){
			$where_str .=" and w.edt>'$bdt1'";
		}
		if( !empty($edt1) ){
			$where_str .=" and w.edt<'$edt1'";
		}		
		
		if( !empty($status) ){
			$where_str .=" and w.status='$status'";
		}		
		
		$cus_name="";
		$cusID = $this->_REQUEST("cusID");
		if(!empty($cusID) ){
			$where_str .=" and w.cusID='$cusID'";
		}
		
		//**************************************************************************
		$countSql    = "select s.name as cst_name ,w.* from cst_website as w,cst_customer as s where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select s.name as cst_name ,w.* from cst_website as w,cst_customer as s
						where $where_str 
						order by w.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,'status'=>$status,
							 'searchKeyword'=>$searchKeyword,'searchValue'=>$searchValue,
							'bdt'=>$bdt,'edt'=>$edt,'bdt1'=>$bdt1,'edt1'=>$edt1,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function cst_website_show(){
			$assArr  		= $this->cst_website();
			$smarty  		= $this->setSmarty();
			$assArr["status_arr"] = $this->cst_website_status();
			$smarty->assign($assArr);
			$smarty->display('cst_website/cst_website_show.html');	
	}

	
	//box在显示
	public function cst_website_show_box(){
			$assArr  		  = $this->cst_website();
			$assArr["status"] = $this->cst_website_status();
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_website/cst_website_show_box.html');	
	}		
	
	//网站增加
	public function cst_website_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");
		if(empty($_POST)){
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$smarty = $this->setSmarty();
			$smarty->assign(array("cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_website/cst_website_add.html');	
		}else{
			$dt	   = date("Y-m-d H:i:s",time());
			$cusID	= $this->_REQUEST("org_id");
			$sql   = "insert into cst_website(name,cusID,url,
												icp_account,icp_pwd,icp_num,
												ftp_ip,ftp_account,ftp_pwd,
												bdt,edt,
												intro,adt,create_userID) 
								values('$_POST[name]','$cusID','$_POST[url]',
										'$_POST[icp_account]','$_POST[icp_pwd]','$_POST[icp_num]',
										'$_POST[ftp_ip]','$_POST[ftp_account]','$_POST[ftp_pwd]',
										'$_POST[bdt]','$_POST[edt]',
										'$_POST[intro]','$dt','".SYS_USER_ID."');";										
			if($this->C($this->cacheDir)->update($sql)){
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstWebsite/cst_website_show/");
			}	
		}
	}		
	
	//网站续费
	public function cst_website_add_renew(){
			$id		=$this->_REQUEST("id");
			if(empty($_POST)){
				$number =date("ymdh").rand(10,99);
				$website=$this->cst_website_get_one($id);
				$smarty = $this->setSmarty();
				$smarty->assign(array("number"=>$number,"website"=>$website));
				$smarty->display('cst_website/cst_website_add_renew.html');		
			}else{
				$data["con_number"] =$this->_REQUEST("con_number");	
				$data["cusID"]		= $this->_REQUEST("cus_id");
				$data["linkmanID"]	= $this->_REQUEST("linkman_id");//客户代表
				$data["chanceID"]  = $this->_REQUEST("chance_id");
				$data["our_userID"]	= $this->_REQUEST("our_id");//我方代表
				$data["title"]	   = $this->_REQUEST("title");
				$data["websiteID"]	= $this->_REQUEST("websiteID");
				$data["renew_status"]= $this->_REQUEST("renew_status");
				$data["money"]	= $this->_REQUEST("money");
				$data["bdt"]	= $this->_REQUEST("bdt");
				$data["edt"]	= $this->_REQUEST("edt");
				$data["intro"]	= $this->_REQUEST("intro");
				//增加合同
				$rtn=$this->L("SalContract")->sal_contract_add_save($data);
				if($rtn){
					$usql="update cst_website set edt='".$data["edt"]."' where id='$id'";//更新时间
					$this->C($this->cacheDir)->updt($usql);
					$this->L("Common")->ajax_json_success("操作成功",'2',"/CstWebsite/cst_website_show/");
				}
												
			}
	}	
	
	public function cst_website_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from cst_website where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer));
			$smarty->display('cst_website/cst_website_modify.html');	
		}else{//更新保存数据
			$cusID   = $this->_REQUEST("org_id");
			$sql= "update cst_website set 
							name='$_POST[name]',cusID='$cusID',url='$_POST[url]',
							ftp_ip='$_POST[ftp_ip]',ftp_account='$_POST[ftp_account]',ftp_pwd='$_POST[ftp_pwd]',
							icp_account='$_POST[icp_account]',icp_pwd='$_POST[icp_pwd]',icp_num='$_POST[icp_num]',
							intro='$_POST[intro]'
			 		where id='$id'";
			if($this->C($this->cacheDir)->update($sql)>=0){
				$this->L("Common")->ajax_json_success("操作成功",'2',"/CstWebsite/cst_website_show/");
			}		
		}
	}
	
	//查询一条记录
	public function cst_website_get_one($id=""){
		if($id){
			$sql = "select * from cst_website where id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}	
	
	public function cst_website_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_website where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstWebsite/cst_website_show/");	
	}	
	
	public function cst_website_select(){
		$cusID  = $this->_REQUEST("cusID");
		$sql	="select id,name from cst_website where cusID='$cusID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
		
	public function cst_website_arr($cusID=""){
		$rtArr  =array();
		$where  =empty($cusID)?"":" where cusID='$cusID'";
		$sql	="select id,name from cst_website $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}
	
	//网站状态
	public function cst_website_status(){
		return  array(
				"1"=>"<b style='color:#19A15F;'>有效</b>",
				"2"=>"<b style='color:#0000FF;'>续约</b>",
				"3"=>"<b style='color:#DD4E41;'>流失</b>"
		);
	}
					
}//end class
?>