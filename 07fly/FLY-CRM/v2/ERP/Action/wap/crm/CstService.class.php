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
class CstService extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		//_instance('Action/wap/Auth');
		$this->dict=_instance('Action/crm/CstDict');
		$this->customer=_instance('Action/crm/CstCustomer');
	}	
	
	public function cst_service(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?$GLOBALS["pageSize"]:$pageSize;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$customer_name 	= $this->_REQUEST("customer_name");
		$customer_id 	= $this->_REQUEST("customer_id");
		$content	   = $this->_REQUEST("content");
		$where_str = " s.customer_id=c.customer_id and c.owner_user_id in (".SYS_USER_VIEW.")";

		if( !empty($content) ){
			$where_str .=" and s.content like '%$content%'";
		}
		if( !empty($customer_id) ){
			$where_str .=" and s.customer_id = '$customer_id'";
		}
		if( !empty($customer_name) ){
			$where_str .=" and c.name like '%$customer_name%'";
		}			
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_customer' ){
			$order_by .=" s.customer_id $orderDirection";
		}else if($orderField=='by_service'){
			$order_by .=" s.service_time $orderDirection";
		}else{
			$order_by .=" s.service_id desc";
		}		
		//**************************************************************************
		$countSql    = "select c.name as customer_name ,s.* from cst_service as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select c.name as customer_name ,s.* from cst_service as s,cst_customer as c
						where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['services_name']=$this->dict->cst_dict_get_name($row['services']);
			$list[$key]['servicesmodel_name']=$this->dict->cst_dict_get_name($row['servicesmodel']);
		}		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"statusCode"=>200);
		return $assignArray;
		
	}
	public function cst_service_json(){
		$assArr = $this->cst_service();
		echo json_encode($assArr);
	}	
	public function cst_service_show(){
		$smarty	= $this->setSmarty();
		$smarty->display('crm/cst_service_show.html');	
	}		

	public function cst_service_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$services=$this->dict->cst_dict_list('services');
			$servicesmodel=$this->dict->cst_dict_list('servicesmodel');
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer,"services"=>$services,"servicesmodel"=>$servicesmodel));
			$smarty->display('crm/cst_service_add.html');	
		}else{
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'services'=>$this->_REQUEST("services"),
				'servicesmodel'=>$this->_REQUEST("servicesmodel"),
				'service_time'=>$this->_REQUEST("service_time"),
				'tlen'=>$this->_REQUEST("tlen"),
				'content'=>$this->_REQUEST("content"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('cst_service',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}		
		}
	}		

	public function cst_service_modify(){
		$service_id = $this->_REQUEST("service_id");
		if(empty($_POST)){
			$sql 	= "select * from cst_service where service_id='$service_id'";
			$one	= $this->C($this->cacheDir)->findOne($sql);	
			$customer=$this->customer->cst_customer_list();
			$services=$this->dict->cst_dict_list('services');
			$servicesmodel=$this->dict->cst_dict_list('servicesmodel');
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"services"=>$services,"servicesmodel"=>$servicesmodel));
			$smarty->display('crm/cst_service_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'services'=>$this->_REQUEST("services"),
				'servicesmodel'=>$this->_REQUEST("servicesmodel"),
				'service_time'=>$this->_REQUEST("service_time"),
				'tlen'=>$this->_REQUEST("tlen"),
				'content'=>$this->_REQUEST("content"),
			);
			
			$this->C($this->cacheDir)->modify('cst_service',$into_data,"service_id='$service_id'");
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function cst_service_del(){
		$service_id = $this->_REQUEST("service_id");
		$sql = "delete from cst_service where service_id in ($service_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	public function cst_service_view(){
		$service_id = $this->_REQUEST("service_id");
		$sql 	= "select * from cst_service where service_id='$service_id'";
		$one	= $this->C($this->cacheDir)->findOne($sql);	
		$one['customer']		=$this->customer->cst_customer_get_one($one['customer_id']);
		$one['services_name']	=$this->dict->cst_dict_get_name($one['services']);
		$one['servicesmodel_name']=$this->dict->cst_dict_get_name($one['servicesmodel']);
		$rtnArr =array('one'=>$one,'statusCode'=>'200');
		echo json_encode($rtnArr);	
	}
		
}//end class
?>