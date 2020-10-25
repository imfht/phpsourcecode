<?php
/*
 * 产品管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Product extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function product(){
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
		
		
		$countSql    = "select id from pro_product where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from pro_product  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,"type"=>$type);	
		return $assignArray;
	}
	public function product_show(){
			$assArr    			= $this->product();
			$assArr["supplier"] = $this->L("Supplier")->supplier_arr();
			$assArr["type"]		= $this->L("ProType")->pro_type_arr();
			$smarty   			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('product/product_show.html');	
	}
		
	public function search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('product/search.html');	
	}	
	public function lookup_search(){
			$assArr  		= $this->product();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('product/lookup_search.html');	
	}		
	public function advanced_search(){
		$smarty  = $this->setSmarty();
		//$smarty->assign($article);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('product/advanced_search.html');	
	}		
	
	public function product_add(){
		if(empty($_POST)){
			$type 	= $this->L("ProType")->pro_type_select_tree();
			$smarty = $this->setSmarty();
			$smarty->assign(array("type"=>$type));
			$smarty->display('product/product_add.html');	
		}else{
			$typeID =$this->_REQUEST("district_typeID");
			$dt	    = date("Y-m-d H:i:s",time());
			$supID  = $this->_REQUEST("org_id");
			$sql    = "insert into pro_product(pro_number,name,price,typeID,model,norm,supID,image,visible,intro,adt) 
								values('$_POST[pro_number]','$_POST[name]','$_POST[price]',
										'$typeID','$_POST[model]','$_POST[norm]','$supID',
										'$_POST[image]','$_POST[visible]','$_POST[intro]','$dt');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	public function product_modify(){
		$id	  = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from pro_product where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);
			$supplier	= $this->L("Supplier")->supplier_arr();
			$type 		= $this->L("ProType")->pro_type_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"type"=>$type,"supplier"=>$supplier));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('product/product_modify.html');	
		}else{
			$typeID =$this->_REQUEST("district_typeID");
			$supID =$this->_REQUEST("org_id");
			$sql= "update pro_product set 
								name='$_POST[name]',price='$_POST[price]',
								typeID='$typeID',model='$_POST[model]',norm='$_POST[norm]',
								supID='$supID',image='$_POST[image]',intro='$_POST[intro]',
								visible='$_POST[visible]' 
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/Product/product_show/");			
		}
	}	
	public function product_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from pro_product where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Product/product_show/");	
	}			
}//end class
?>