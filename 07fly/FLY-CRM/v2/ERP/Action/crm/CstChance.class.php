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
class CstChance extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->dict=_instance('Action/crm/CstDict');
		$this->customer=_instance('Action/crm/CstCustomer');
		$this->linkman=_instance('Action/crm/CstLinkman');
	}	
	
	public function cst_chance(){
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
		$salestage		= $this->_REQUEST("salestage");
		
		$where_str	= "s.customer_id=c.customer_id and c.owner_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";
		
		if( !empty($keywords) ){
			$where_str .=" and (s.name like '%$keywords%' or s.mobile like '%$keywords%' or s.tel like '%$keywords%')";
		}
		if(!empty($customer_id) ){
			$where_str .=" and s.customer_id='$customer_id'";
		}
		if(!empty($address) ){
			$where_str .=" and s.address like '%$address%'";
		}
		if(!empty($customer_name) ){
			$where_str .=" and c.name like '%$customer_name%'";
		}
		
		if( !empty($salestage) ){
			$where_str .=" and s.salestage = '$salestage'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and s.adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and s.adt < '$edt'";
		}	
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_customer' ){
			$order_by .=" s.customer_id $orderDirection";
		}else if($orderField=='by_finddate'){
			$order_by .=" s.find_date $orderDirection";
		}else if($orderField=='by_billdate'){
			$order_by .=" s.bill_date $orderDirection";
		}else if($orderField=='by_money'){
			$order_by .=" s.money $orderDirection";
		}else{
			$order_by .=" s.chance_id desc";
		}	
		//**************************************************************************
		$countSql    = "select c.name as customer_name ,s.* from cst_chance as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select c.name as customer_name ,s.* from cst_chance as s,cst_customer as c
						where $where_str $order_by limit $beginRecord,$pageSize";
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$dict		 = $this->dict->cst_dict_arr();
		foreach($list as $key=>$row){
			$list[$key]['salestage_name']=@$dict[$row['salestage']];
			$list[$key]['linkman']	 	 =$this->linkman->cst_linkman_get_one($row['linkman_id']);
			//$list[$key]['status_name']	 	=$status[$row['status']];
			//$list[$key]['create_user_name']	=$this->L("User")->user_get_name($row['create_user_id']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);
		return $assignArray;
		
	}
	public function cst_chance_json(){
		$assArr = $this->cst_chance();
		echo json_encode($assArr);
	}		
	public function cst_chance_show(){
		$salestage=$this->dict->cst_dict_list('salestage');
		$smarty = $this->setSmarty();
		$smarty->assign(array('salestage'=>$salestage));
		$smarty->display('crm/cst_chance_show.html');	
	}	
	
	public function cst_chance_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$salestage=$this->dict->cst_dict_list('salestage');
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer,"salestage"=>$salestage));
			$smarty->display('crm/cst_chance_add.html');	
		
		}else{
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'linkman_id'=>$this->_REQUEST("linkman_id"),
				'find_date'=>$this->_REQUEST("find_date"),
				'bill_date'=>$this->_REQUEST("bill_date"),
				'name'=>$this->_REQUEST("name"),
				'salestage'=>$this->_REQUEST("salestage"),
				'money'=>$this->_REQUEST("money"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('cst_chance',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}		
		}
	}		

	public function cst_chance_modify(){
		$chance_id	= $this->_REQUEST("chance_id");
		if(empty($_POST)){
			$sql 		="select * from cst_chance where chance_id='$chance_id'";
			$one 		=$this->C($this->cacheDir)->findOne($sql);	
			$customer	=$this->customer->cst_customer_list();
			$salestage	=$this->dict->cst_dict_list('salestage');
			$smarty 	=$this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"salestage"=>$salestage));
			$smarty->display('crm/cst_chance_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'linkman_id'=>$this->_REQUEST("linkman_id"),
				'find_date'=>$this->_REQUEST("find_date"),
				'bill_date'=>$this->_REQUEST("bill_date"),
				'name'=>$this->_REQUEST("name"),
				'salestage'=>$this->_REQUEST("salestage"),
				'money'=>$this->_REQUEST("money"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->modify('cst_chance',$into_data,"chance_id='$chance_id'")){
				$this->L("Common")->ajax_json_success("操作成功");
			}				
		}
	}
	
	public function cst_chance_del(){
		$chance_id = $this->_REQUEST("chance_id");
		$sql  = "delete from cst_chance where chance_id in ($chance_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	public function cst_chance_select(){
		$customer_id = $this->_REQUEST("customer_id");
		if(!empty($customer_id)){
			$where_str="where customer_id='$customer_id'";
		}else{
			$where_str="";
		}
		$sql	="select * from cst_chance $where_str";
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
	public function cst_chance_arr($cusID=""){
		$rtArr  =array();
		$where  =empty($cusID)?"":" where cusID='$cusID'";
		$sql	="select id,title from cst_chance $where ";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["title"];
			}
		}
		return $rtArr;
	}
	
	//返回字典名称
	public function cst_chance_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,title from cst_chance where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["title"]."&nbsp;";
			}
		}
		return $str;
	}
	//返回联系人一条记录
	public function cst_chance_get_one($id){
		if(empty($id)) $id=0;
		$sql ="select * from cst_chance where chance_id='$id'";	
		$one =$this->C($this->cacheDir)->findOne($sql);
		return $one;
	}	
	public function cst_chance_status(){
		return array("1"=>"跟踪","2"=>"成功","3"=>"失败","4"=>"搁置","5"=>"失效");
		    
	}			
}//end class
?>