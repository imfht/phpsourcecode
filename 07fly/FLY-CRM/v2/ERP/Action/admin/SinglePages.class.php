<?php
/*
 *
 * admin.SinglePages  单页管理   
 *
 * =========================================================
 * 零起飞网络 - 专注于网站建设服务和软件定制
 * 以质量求生存，以服务谋发展，以信誉创品牌 !
 * ----------------------------------------------
 * @copyright	Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license    For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author ：kfrs <goodkfrs@QQ.com>
 * @version ：1.0
 * @link ：http://www.07fly.top
 */	
class SinglePages extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		//$this->auth=_instance('Action/sysmanage/Auth');
	}	
	public function single_pages(){
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage  = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$tel	   = $this->_REQUEST("tel");
		$where_str = "id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
	
		//**************************************************************************
		$countSql    = "select id from fly_single_pages where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_single_pages  where $where_str order by sort asc, id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function single_pages_show(){
			$assArr  = $this->single_pages();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/single_pages_show.html');	
	}
	public function single_pages_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/single_pages_add.html');	
		}else{
			$title    = $this->_REQUEST("title");
			$keywords  = $this->_REQUEST("keywords");
			$description= $this->_REQUEST("description");
			$temp     = $this->_REQUEST("temp");
			$content   = $this->_REQUEST("content");
			$sort     = $this->_REQUEST("sort");
			$dt	= date("Y-m-d H:i:s",time());
			$sql = "insert into fly_single_pages(title,keywords,description,temp,content,sort,adt) 
								values('$title','$keywords','$description','$temp','$content','$sort','$dt');";

			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$this->location("操作成功","/admin/SinglePages/single_pages_show/");	
			}else{
				$this->location("操作失败","",2);	
			}			
		}
	}		
	
	
	public function single_pages_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_single_pages where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/single_pages_modify.html');	
		}else{//更新保存数据
			$title    = $this->_REQUEST("title");
			$keywords  = $this->_REQUEST("keywords");
			$description= $this->_REQUEST("description");
			$temp     = $this->_REQUEST("temp");
			$content   = $this->_REQUEST("content");
			$sort     = $this->_REQUEST("sort");
			$sql= "update fly_single_pages set 
							title='$title',
							keywords='$keywords',
							description='$description',
							temp='$temp',
							sort='$sort',
							content='$content'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->location("操作成功","/admin/SinglePages/single_pages_show/");		
		}
	}
	public function single_pages_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_single_pages where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->location("操作成功","/admin/SinglePages/single_pages_show/");	
	}	
	

}//
?>