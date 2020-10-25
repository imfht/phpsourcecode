<?php
/*
 * 供应商管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Supplier extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function supplier(){
	
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
		$countSql    = "select id from sup_supplier where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from sup_supplier  where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
		
	}
	
	public function supplier_show(){
			$assArr  		= $this->supplier();
			$assArr["dict"] = $this->L("CstDict")->cst_dict_arr();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('supplier/supplier_show.html');	
	}		
	public function lookup_search(){
			$assArr  		= $this->supplier();
			$assArr["dict"] = $this->L("CstDict")->cst_dict_arr();
			$smarty  		= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('supplier/lookup_search.html');	
	}	
	
	public function advanced_search(){
		$smarty  = $this->setSmarty();
		$smarty->display('supplier/advanced_search.html');	
	}	
	
	public function supplier_add(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('supplier/supplier_add.html');	
		}else{
			$dt	     = date("Y-m-d H:i:s",time());
			$ecotype = $this->_REQUEST("org1_id");
			$trade   = $this->_REQUEST("org2_id");
			$sql     = "insert into sup_supplier(name,website,ecotype,trade,linkman,tel,fax,email,zipcode,address,intro,adt) 
								values('$_POST[name]','$_POST[website]',
										'$ecotype','$trade','$_POST[linkman]','$_POST[tel]',
										'$_POST[fax]','$_POST[email]','$_POST[zipcode]','$_POST[address]','$_POST[intro]','$dt');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	
	public function supplier_modify(){
		$id	  	 = $this->_REQUEST("id");
		$ecotype = $this->_REQUEST("org1_id");
		$trade   = $this->_REQUEST("org2_id");
		
		if(empty($_POST)){
			$sql 		= "select * from sup_supplier where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$smarty  	= $this->setSmarty();
			$dict	    = $this->L("CstDict")->cst_dict_arr();
			$smarty->assign(array("one"=>$one,"dict"=>$dict));
			$smarty->display('supplier/supplier_modify.html');	
		}else{//更新保存数据
			$sql= "update sup_supplier set 
							name='$_POST[name]',website='$_POST[website]',
							ecotype='$ecotype',trade='$trade',
							linkman='$_POST[linkman]',tel='$_POST[tel]',fax='$_POST[fax]',email='$_POST[email]',
							zipcode='$_POST[zipcode]',address='$_POST[address]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function supplier_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from sup_supplier where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/Supplier/supplier_show/");	
	}	
	
	public function supplier_arr(){
		$rtArr  =array();
		$sql	="select id,name from sup_supplier";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}		
	
	
	//根据传的ID编号得到名称
	public function supplier_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,name from sup_supplier where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
			
}// end  class
?>