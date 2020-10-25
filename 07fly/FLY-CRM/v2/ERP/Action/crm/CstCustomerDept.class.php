<?php
/*
 *
 * crm.CstCustomerDept  客户管理部门客户   
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
class CstCustomerDept extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->dict=_instance('Action/crm/CstDict');
		$this->comm=_instance('Extend/Common');
		$this->field_ext=_instance('Action/crm/CstFieldExt');
		$this->sys_user=_instance('Action/sysmanage/User');
	}	
	
	public function cst_customer_dept(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$name	   = $this->_REQUEST("name");
		$conn_time	= $this->_REQUEST("conn_time");
		$next_time	= $this->_REQUEST("next_time");
		$edt   	  = $this->_REQUEST("edt");
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		
		$where_str = " c.owner_user_id in(".SYS_USER_SUB_ID.")";
		
		$searchKeyword	= $this->_REQUEST("searchKeyword");
		$searchValue	= $this->_REQUEST("searchValue");
		if( !empty($searchValue) ){
			$where_str .=" and c.name like '%$name%'";
		}
		if( !empty($name) ){
			$where_str .=" and c.name like '%$name%'";
		}
		
		//到期时间
		if( !empty($conn_time) ){
			$date_range=$this->comm->date_range('-1',$conn_time);
			$where_str .=" and c.conn_time>'$date_range' and c.conn_time<>'0000-00-00 00:00:00'";
		}

		//下次联系
		if( !empty($next_time) ){
			$date_range=$this->comm->date_range('1',$next_time);
			$where_str .=" and c.next_time<'$date_range' and c.next_time>='".NOWTIME."'";
		}
		
		$order_by="order by";
		if( $orderField=='by_nextbdt' ){
			$order_by .=" c.next_time $orderDirection";
		}else if($orderField=='by_connbdt'){
			$order_by .=" c.conn_time $orderDirection";
		}else if($orderField=='by_customer'){
			$order_by .=" c.customer_id $orderDirection";
		}else{
			$order_by .=" c.customer_id desc";
		}
		
		//**************************************************************************
		$countSql   = "select c.* from cst_customer as c where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select c.* from cst_customer as c where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$dict		 = $this->dict->cst_dict_arr();
		foreach($list as $key=>$row){
			$list[$key]['owner_user']=$this->sys_user->user_get_one($row['owner_user_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);		
		return $assignArray;
	}
	//返回客户id.名称主要用于下拉选择搜索
	public function cst_customer_dept_list(){
		$where_str = " c.owner_user_id in(".SYS_USER_SUB_ID.")";
		$sql ="select customer_id,name from cst_customer as c where $where_str";
		$list=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}		
	public function cst_customer_dept_json(){
		$assArr  = $this->cst_customer_dept();
		echo json_encode($assArr);
	}	
	//客户列表显示
	public function cst_customer_dept_show(){
		$smarty = $this->setSmarty();
		$smarty->display('crm/cst_customer_dept_show.html');	
	}	

}//end class
?>