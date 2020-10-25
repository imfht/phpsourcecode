<?php
/*
 * 供应商联系人类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class SupLinkman extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		$this->dict=_instance('Action/crm/CstDict');
		$this->supplier=_instance('Action/erp/SupSupplier');
		$this->field_ext=_instance('Action/crm/CstFieldExt');
	}	
	
	public function sup_linkman(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$keywords		= $this->_REQUEST("keywords");
		$supplier_id	=$this->_REQUEST("supplier_id");
		$supplier_name	= $this->_REQUEST("supplier_name");
		$address	= $this->_REQUEST("address");
		$where_str = " l.supplier_id=s.supplier_id ";
		if( !empty($keywords) ){
			$where_str .=" and (l.name like '%$keywords%' or l.mobile like '%$keywords%' or l.tel like '%$keywords%')";
		}
		if(!empty($supplier_id) ){
			$where_str .=" and l.supplier_id='$supplier_id'";
		}
		if(!empty($address) ){
			$where_str .=" and l.address like '%$address%'";
		}
		if(!empty($supplier_name) ){
			$where_str .=" and s.name like '%$supplier_name%'";
		}		
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_supplier' ){
			$order_by .=" l.supplier_id $orderDirection";
		}else if($orderField=='by_connbdt'){
			$order_by .=" l.conn_time $orderDirection";
		}else{
			$order_by .=" l.supplier_id desc";
		}
		//**************************************************************************
		$countSql    = "select s.name as supplier_name ,l.* from sup_linkman as l,sup_supplier as s where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select s.name as supplier_name ,l.* from sup_linkman as l,sup_supplier as s
						where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function sup_linkman_json(){
		$assArr  = $this->sup_linkman();
		echo json_encode($assArr);
	}	
	public function sup_linkman_show(){
		$assArr  		= $this->sup_linkman();
		$smarty  		= $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('erp/sup_linkman_show.html');	
	}		
	
	public function sup_linkman_add(){
		$supplier_id= $this->_REQUEST("supplier_id");
		if(empty($_POST)){
			$supplier=$this->supplier->sup_supplier_list();
			$field_ext=$this->field_ext->cst_field_ext_html('sup_linkman');//扩展字段
			$smarty = $this->setSmarty();
			$smarty->assign(array("supplier_id"=>$supplier_id,"supplier"=>$supplier,"field_ext"=>$field_ext));
			$smarty->display('erp/sup_linkman_add.html');	
		}else{
			$into_data=array(
				'supplier_id'=>$this->_REQUEST("supplier_id"),
				'name'=>$this->_REQUEST("name"),
				'gender'=>$this->_REQUEST("gender"),
				'postion'=>$this->_REQUEST("postion"),
				'mobile'=>$this->_REQUEST("mobile"),
				'tel'=>$this->_REQUEST("tel"),
				'qicq'=>$this->_REQUEST("qicq"),
				'email'=>$this->_REQUEST("email"),
				'address'=>$this->_REQUEST("address"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);	
			//******************************************************
			//处理扩展字段
			//合并主表数据和扩展字段数据
			$fields=$this->field_ext->cst_field_ext_list('sup_linkman');
			$ext_data=array();
			foreach($fields as $row){
				$field=$row['field_name'];
				$ext_data=array_merge($ext_data,array("$field"=>$this->_REQUEST($field)));
			}
			$into_data=array_merge($into_data,$ext_data);
			//******************************************************	
			if($this->C($this->cacheDir)->insert('sup_linkman',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}	
		}
	}

	public function sup_linkman_modify(){
		$linkman_id = $this->_REQUEST("linkman_id");
		if(empty($_POST)){
			$sql 		= "select * from sup_linkman where linkman_id='$linkman_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$supplier=$this->supplier->sup_supplier_list();
			//扩展字段操作
			$field_ext=$this->field_ext->cst_field_ext_html('sup_linkman',$one);
			$option	=$this->field_ext->cst_field_ext_option('sup_linkman','option');
			$options=array();
			foreach($option as $k){
				$options[$k]=$one[$k];
			}
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"supplier"=>$supplier,"field_ext"=>$field_ext,"options"=>$options));
			$smarty->display('erp/sup_linkman_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'supplier_id'=>$this->_REQUEST("supplier_id"),
				'name'=>$this->_REQUEST("name"),
				'gender'=>$this->_REQUEST("gender"),
				'postion'=>$this->_REQUEST("postion"),
				'mobile'=>$this->_REQUEST("mobile"),
				'tel'=>$this->_REQUEST("tel"),
				'qicq'=>$this->_REQUEST("qicq"),
				'email'=>$this->_REQUEST("email"),
				'address'=>$this->_REQUEST("address"),
				'intro'=>$this->_REQUEST("intro"),
			);
			//******************************************************
			//处理扩展字段
			//合并主表数据和扩展字段数据
			$fields=$this->field_ext->cst_field_ext_list('sup_linkman');
			$ext_data=array();
			foreach($fields as $row){
				$field=$row['field_name'];
				$ext_data=array_merge($ext_data,array("$field"=>$this->_REQUEST($field)));
			}
			$into_data=array_merge($into_data,$ext_data);
			//******************************************************	
			$this->C($this->cacheDir)->modify('sup_linkman',$into_data,"linkman_id='$linkman_id'");
			$this->L("Common")->ajax_json_success("操作成功");
		}
	}
	
		
	public function sup_linkman_del(){
		$id	  = $this->_REQUEST("linkman_id");
		$sql  = "delete from sup_linkman where linkman_id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	public function sup_linkman_select(){
		$customer_id = $this->_REQUEST("customer_id");
		if(!empty($customer_id)){
			$where_str="where supplier_id='$customer_id'";
		}else{
			$where_str="";
		}
		$sql	="select * from sup_linkman $where_str";
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
		
	public function sup_linkman_arr($supID=""){
		$rtArr  =array();
		$where  =empty($supID)?"":" where supID='$supID'";
		$sql	="select id,name from sup_linkman $where";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}	 	
	//返回联系人一条记录
	public function sup_linkman_get_one($id){
		if(empty($id)) $id=0;
		$sql ="select * from sup_linkman where linkman_id='$id'";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}			
}// end class 
?>