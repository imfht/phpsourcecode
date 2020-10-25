<?php
/*
 *
 * crm.CstLinkMan  客户联系人管理   
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
class CstLinkman extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->customer=_instance('Action/crm/CstCustomer');
		$this->field_ext=_instance('Action/crm/CstFieldExt');
	}	
	
	public function cst_linkman($cusID=0){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询

		$keywords		= $this->_REQUEST("keywords");
		$customer_id	=$this->_REQUEST("customer_id");
		$customer_name	= $this->_REQUEST("customer_name");
		$address	= $this->_REQUEST("address");
		
		$where_str	= "l.customer_id=s.customer_id and s.owner_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";

		if( !empty($keywords) ){
			$where_str .=" and (l.name like '%$keywords%' or l.mobile like '%$keywords%' or l.tel like '%$keywords%')";
		}
		if(!empty($customer_id) ){
			$where_str .=" and l.customer_id='$customer_id'";
		}
		if(!empty($address) ){
			$where_str .=" and l.address like '%$address%'";
		}
		if(!empty($customer_name) ){
			$where_str .=" and s.name like '%$customer_name%'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_customer' ){
			$order_by .=" l.customer_id $orderDirection";
		}else if($orderField=='by_connbdt'){
			$order_by .=" l.conn_time $orderDirection";
		}else{
			$order_by .=" l.linkman_id desc";
		}		
		//**************************************************************************
		$countSql   = "select s.name as customer_name ,l.* from cst_linkman as l,cst_customer as s where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select s.name as customer_name ,l.* from cst_linkman as l,cst_customer as s
						where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
		
	}
	public function cst_linkman_json(){
		$assArr  = $this->cst_linkman();
		echo json_encode($assArr);
	}
	//浏览
	public function cst_linkman_show(){
		$assArr  		= $this->cst_linkman();
		$smarty  		= $this->setSmarty();
		$smarty->assign($assArr);
		$smarty->display('crm/cst_linkman_show.html');	
	}	
	//添加
	public function cst_linkman_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$field_ext=$this->field_ext->cst_field_ext_html('cst_linkman');//扩展字段
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer,"field_ext"=>$field_ext));
			$smarty->display('crm/cst_linkman_add.html');	
		}else{
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
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
			$fields=$this->field_ext->cst_field_ext_list('cst_linkman');
			$ext_data=array();
			foreach($fields as $row){
				$field=$row['field_name'];
				$ext_data=array_merge($ext_data,array("$field"=>$this->_REQUEST($field)));
			}
			$into_data=array_merge($into_data,$ext_data);
			//******************************************************	
			if($this->C($this->cacheDir)->insert('cst_linkman',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}	
		}
	}		
	
	//更新
	public function cst_linkman_modify(){
		$linkman_id = $this->_REQUEST("linkman_id");
		if(empty($_POST)){
			$sql 		= "select * from cst_linkman where linkman_id='$linkman_id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			//扩展字段操作
			$field_ext=$this->field_ext->cst_field_ext_html('cst_linkman',$one);
			$option	=$this->field_ext->cst_field_ext_option('cst_linkman','option');
			$options=array();
			foreach($option as $k){
				$options[$k]=$one[$k];
			}
			$customer	=$this->customer->cst_customer_list();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"field_ext"=>$field_ext,"options"=>$options));
			$smarty->display('crm/cst_linkman_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
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
			$fields=$this->field_ext->cst_field_ext_list('cst_linkman');
			$ext_data=array();
			foreach($fields as $row){
				$field=$row['field_name'];
				$ext_data=array_merge($ext_data,array("$field"=>$this->_REQUEST($field)));
			}
			$into_data=array_merge($into_data,$ext_data);
			//******************************************************
			$this->C($this->cacheDir)->modify('cst_linkman',$into_data,"linkman_id='$linkman_id'");
			$this->L("Common")->ajax_json_success("操作成功");
		}
	}
	
	//删除	
	public function cst_linkman_del(){
		$id	  = $this->_REQUEST("linkman_id");
		$sql  = "delete from cst_linkman where linkman_id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	public function cst_linkman_select(){
		$customer_id = $this->_REQUEST("customer_id");
		if(!empty($customer_id)){
			$where_str="where customer_id='$customer_id'";
		}else{
			$where_str="";
		}
		$sql	="select * from cst_linkman $where_str";
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}
	//返回联系人一条记录
	public function cst_linkman_get_one($id){
		if(empty($id)) $id=0;
		$sql ="select * from cst_linkman where linkman_id='$id'";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}
	
	
	
}//end class
?>