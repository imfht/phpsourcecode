<?php
/*
 *
 * admin.News  新闻管理    
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
class News extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->auth=_instance('Action/sysmanage/Auth');
	}	
	public function news(){
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
		$where_str = "n.id != 0";
		
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and n.$searchKeyword like '%$searchValue%'";
		}
		if( !empty($bdt) ){
			$where_str .=" and n.adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and n.adt < '$edt'";
		}		
		//**************************************************************************
		$countSql    = "select n.id from fly_news as n left join fly_news_type as t on n.type_id=t.id where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select n.*,t.typename from fly_news as n left join fly_news_type as t on n.type_id=t.id
						where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function news_show(){
			$assArr  = $this->news();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('admin/news_show.html');	
	}
	public function news_add(){
		if(empty($_POST)){
			$news_type_opt=$this->L("admin/NewsType")->news_type_get_opt("type_id");
			$smarty = $this->setSmarty();
			$smarty->assign(array("news_type_opt"=>$news_type_opt));
			$smarty->display('admin/news_add.html');	
		}else{
			$title    = $this->_REQUEST("title");
			$type_id    = $this->_REQUEST("type_id");
			$keywords   = $this->_REQUEST("keywords");
			$description   = $this->_REQUEST("description");
			$defaultimg   = $this->_REQUEST("img_spath");
			$sort   = $this->_REQUEST("sort");
			$content   = $this->_REQUEST("content");
			$dt	     = date("Y-m-d H:i:s",time());
			
			$sql     = "insert into fly_news(title,type_id,keywords,description,defaultimg,sort,content,adt) 
								values('$title','$type_id','$keywords','$description','$defaultimg','$sort','$content','$dt');";

			$rtn=$this->C($this->cacheDir)->update($sql);
			if($rtn>0){
				$this->location("操作成功","/admin/News/news_show/");	
			}else{
				$this->location("操作失败","",2);	
			}			
		}
	}		
	
	
	public function news_modify(){
		$id	  	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select n.*,t.typename from fly_news as n left join fly_news_type as t on n.type_id=t.id where n.id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$news_type_opt=$this->L("admin/NewsType")->news_type_get_opt("type_id",$one['type_id']);
			$smarty 	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,'news_type_opt'=>$news_type_opt));
			$smarty->display('admin/news_modify.html');	
		}else{//更新保存数据
			$sql= "update fly_news set 
							title='$_POST[title]',
							content='$_POST[content]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->location("操作成功","/admin/News/news_show/");		
		}
	}
	
		
	public function news_del(){
		$id	  = $this->_REQUEST("id");
		$sql  = "delete from fly_news where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->location("操作成功","/admin/News/news_show/");	
	}	
	

}//
?>