<?php
/*
 *
 * admin.Notice  通知管理   
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
class Notice extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		//$this->auth=_instance('Action/sysmanage/Auth');
	}	
	public function notice(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage  = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$tel	   = $this->_REQUEST("tel");
		$linkman   = $this->_REQUEST("linkman");
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
		
		if( !empty($title) ){
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
		$countSql    = "select id from fly_notice where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_notice  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function notice_show(){
			$assArr  = $this->notice();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/notice_show.html');	
	}
	public function notice_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/notice_add.html');	
		}else{
			$title    = $this->_REQUEST("title");
			$content   = $this->_REQUEST("content");
			$dt	     = date("Y-m-d H:i:s",time());
			
			$sql     = "insert into fly_notice(title,content,adt) 
								values('$title','$content','$dt');";

			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$this->L("Common")->ajax_json_success("操作成功","2","/admin/Notice/notice_show/");	
			}else{
				$this->L("Common")->ajax_error("操作失败");	
			}			
		}
	}		
	
	
	public function notice_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_notice where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/notice_modify.html');	
		}else{//更新保存数据
			$sql= "update fly_notice set 
							title='$_POST[title]',
							content='$_POST[content]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","2","/admin/Notice/notice_show/");		
		}
	}
	
		
	public function notice_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from fly_notice where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/admin/Notice/notice_show/");	
	}	
	

}//
?>