<?php	 
/*
 *
 * admin.NewsType 新闻分类  
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
class NewsType extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
	}	
	
	public function news_type(){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$type		 = $this->_REQUEST("type");
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//*********************************************************************
		$bdt   	   = $this->_REQUEST("bdt");
		$edt   	   = $this->_REQUEST("edt");
				
		$where_str = "id != 0";
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**********************************************************************
		
		
		$countSql   = "select id from fly_news_type where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fly_news_type  where $where_str order by sort asc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,
							"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function news_type_show(){
			$assArr    			= $this->news_type();
			$smarty   			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/news_type_show.html');	
	}
		
	public function search(){
		$smarty  = $this->setSmarty();
		$smarty->display('news_type/search.html');	
	}	
	public function news_type_lookup_search(){
			$assArr  		= $this->news_type();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/news_type_lookup_search.html');	
	}	
	
	//增加
	public function news_type_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('admin/news_type_add.html');	
		}else{
			$typename	  = $this->_REQUEST("typename");
			$typedir	  = $this->_REQUEST("typedir");
			$typetag	  = $this->_REQUEST("typetag");
			$sort		  = $this->_REQUEST("sort");
			$seotitle	  = $this->_REQUEST("seotitle");
			$keywords	  = $this->_REQUEST("keywords");
			$intro		  = $this->_REQUEST("intro");

			$sql= "insert into fly_news_type(typename,typedir,sort,typetag,seotitle,keywords,intro) 
								values('$typename','$typedir','$sort','$typetag','$seotitle','$keywords','$intro');";
			$this->C($this->cacheDir)->update($sql);	
			$this->location("操作成功","/admin/NewsType/news_type_show/");		
		}
	}		
	public function news_type_modify(){
		$id	  = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fly_news_type where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one));
			$smarty->display('admin/news_type_modify.html');	
		}else{
			$typename	  = $this->_REQUEST("typename");
			$typedir	  = $this->_REQUEST("typedir");
			$typetag	  = $this->_REQUEST("typetag");
			$sort		  = $this->_REQUEST("sort");
			$seotitle	  = $this->_REQUEST("seotitle");
			$keywords	  = $this->_REQUEST("keywords");
			$intro		  = $this->_REQUEST("intro");
			
			$sql= "update fly_news_type set 
								typename='$typename',
								typedir='$typedir',
								typetag='$typetag',
								sort='$sort',
								seotitle='$seotitle',
								keywords='$keywords',
								intro='$intro'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->location("操作成功","/admin/NewsType/news_type_show/");			
		}
	}	
	public function news_type_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_news_type where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->location("操作成功","/admin/NewsType/news_type_show/");	
	}	
	
	
	public function news_type_get_one($id){
		$sql = "select * from fly_news_type where id='$id'";	
		$pak = $this->C($this->cacheDir)->findOne($sql);	
		return $pak;
	}
	
	//分类下拉选择
	public function news_type_get_opt($inputname,$value=null){
		$sql = "select * from fly_news_type;";
		$list= $this->C($this->cacheDir)->findAll($sql);
		$string		="<select name='$inputname' id='$inputname'  class=\"form-control\">";
		foreach($list as $key=>$row){
			$string.="<option value='$row[id]'";
			if($row["id"]==$value) $string.=" selected";
			$string.=">".$row["typename"]."</option>";
		}
		$string.="</select>";
		return $string;
	}	
	public function news_type_lookup(){
		$sql = "select id,typename from fly_news_type;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	//传入ID返回名字
	public function news_type_get_name($id){
		if(empty($id)) $id=0;
		$sql ="select * from fly_news_type where id in ($id)";
		$list=$this->C($this->cacheDir)->findAll($sql);
		$str ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "".$row["typename"]."&nbsp;";
			}
		}
		return $str;
	}
}//
?>