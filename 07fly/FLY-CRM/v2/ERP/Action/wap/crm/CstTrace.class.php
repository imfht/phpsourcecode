<?php
/*
 *
 * crm.CstLinkMan  客户跟踪管理   
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
class CstTrace extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/wap/Auth');
		//$this->msg=_instance('Action/wap/crm/Message');
		$this->dict=_instance('Action/wap/crm/CstDict');
		$this->customer=_instance('Action/wap/crm/CstCustomer');
		$this->linkman=_instance('Action/wap/crm/CstLinkman');
		$this->chance=_instance('Action/wap/crm/CstChance');
	}	
	public function cst_trace(){
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
		
		$where_str	= "t.customer_id=c.customer_id and c.owner_user_id in (".SYS_USER_VIEW.")";
		if(!empty($customer_id) ){
			$where_str .=" and t.customer_id='$customer_id'";
		}
		if( !empty($cus_name) ){
			$where_str .=" and c.name like '%$cus_name%'";
		}		
		if( !empty($bdt) ){
			$where_str .=" and t.adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and t.adt < '$edt'";
		}
		
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_customer' ){
			$order_by .=" t.customer_id $orderDirection";
		}else if($orderField=='by_conn_time'){
			$order_by .=" t.conn_time $orderDirection";
		}else if($orderField=='by_next_time'){
			$order_by .=" t.next_time $orderDirection";
		}else{
			$order_by .=" t.trace_id desc";
		}
		//**************************************************************************
		$countSql   = "select c.name as customer_name ,t.* from cst_trace as t 
						left join cst_customer as c on t.customer_id=c.customer_id
						where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select c.name as customer_name ,t.* from cst_trace as t
						left join cst_customer as c on t.customer_id=c.customer_id
						where $where_str $order_by limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$dict		 = $this->dict->cst_dict_arr();
		foreach($list as $key=>$row){
			$list[$key]['salestage_name']=($row['salestage']>0)?$dict[$row['salestage']]:"";
			$list[$key]['salemode_name']=($row['salemode']>0)?$dict[$row['salemode']]:"";
			$list[$key]['linkman']	 	=$this->linkman->cst_linkman_get_one($row['linkman_id']);
			$list[$key]['chance']	 	=$this->chance->cst_chance_get_one($row['chance_id']);
			//$list[$key]['create_user_name']	=$this->L("User")->user_get_name($row['create_userID']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"statusCode"=>'200');	
		return $assignArray;
		
	}
	public function cst_trace_json(){
		$assArr  = $this->cst_trace();
		$assArr['statusCode'] = '200';
		echo json_encode($assArr);
	}	
	public function cst_trace_show(){
		$salestage=$this->dict->cst_dict_list('salestage');
		$smarty = $this->setSmarty();
		$smarty->assign(array('salestage'=>$salestage));
		$smarty->display('crm/cst_trace_show.html');	
	}		
	public function cst_trace_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$salestage=$this->dict->cst_dict_list('salestage');
			$salemode =$this->dict->cst_dict_list('salemode');
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer,"salestage"=>$salestage,"salemode"=>$salemode));
			$smarty->display('crm/cst_trace_add.html');	
		}else{
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'linkman_id'=>$this->_REQUEST("linkman_id"),
				'chance_id'=>$this->_REQUEST("chance_id"),
				'conn_time'=>$this->_REQUEST("conn_time"),
				'next_time'=>$this->_REQUEST("next_time"),
				'name'=>$this->_REQUEST("name"),
				'salestage'=>$this->_REQUEST("salestage"),
				'salemode'=>$this->_REQUEST("salemode"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('cst_trace',$into_data)){
				$upt_data=array(
					'conn_time'=>$this->_REQUEST("conn_time"),
					'conn_body'=>$this->_REQUEST("intro"),
					'next_time'=>$this->_REQUEST("next_time"),
				);
				$this->C($this->cacheDir)->modify('cst_customer',$upt_data,"customer_id='$customer_id'");
				//增加消息提醒
				$next_time=$this->_REQUEST('next_time');
				if(!empty($next_time) && $next_time<>'0000-00-00'){
					$this->msg->message_add(SYS_USER_ID,'预约联系',$this->_REQUEST("name"),'cst_customer',$customer_id,$next_time);
				}				
				//$this->L("Common")->ajax_json_success("操作成功");
			}
		}
	}		
	
	
	public function cst_trace_modify(){
		$trace_id = $this->_REQUEST("trace_id");
		if(empty($_POST)){
			$sql 	= "select * from cst_trace where trace_id='$trace_id'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);	
			$customer=$this->customer->cst_customer_list();
			$salestage=$this->dict->cst_dict_list('salestage');
			$salemode =$this->dict->cst_dict_list('salemode');
			$smarty = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"salestage"=>$salestage,"salemode"=>$salemode));
			$smarty->display('crm/cst_trace_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'linkman_id'=>$this->_REQUEST("linkman_id"),
				'chance_id'=>$this->_REQUEST("chance_id"),
				'conn_time'=>$this->_REQUEST("conn_time"),
				'next_time'=>$this->_REQUEST("next_time"),
				'name'=>$this->_REQUEST("name"),
				'salestage'=>$this->_REQUEST("salestage"),
				'salemode'=>$this->_REQUEST("salemode"),
				'intro'=>$this->_REQUEST("intro"),
			);
			if($this->C($this->cacheDir)->modify('cst_trace',$into_data,"trace_id='$trace_id'")){
				$this->L("Common")->ajax_json_success("操作成功");
			}
		}
	}
	
	//删除
	public function cst_trace_del(){
		$trace_id	  = $this->_REQUEST("trace_id");
		$sql  = "delete from cst_trace where trace_id in ($trace_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}
	
	public function cst_trace_view(){
		$trace_id = $this->_REQUEST("trace_id");
		$sql 	= "select * from cst_trace where trace_id='$trace_id'";
		$one 	= $this->C($this->cacheDir)->findOne($sql);
		$one['customer']=$this->customer->cst_customer_get_one($one['customer_id']);
		$one['salestage_name']=$this->dict->cst_dict_get_name($one['salestage']);
		$one['salemode_name']=$this->dict->cst_dict_get_name($one['salemode']);
		$rtnArr=array("one"=>$one,"statusCode"=>200);
		echo json_encode($rtnArr);
	}
	
	public function cst_trace_list(){
		$customer_id = $this->_REQUEST("customer_id");
		$sql	="select * from cst_trace where customer_id='$customer_id'";
		$list 	=$this->C($this->cacheDir)->findAll($sql);
		return array('list'=>$list);
	}
	
	public function cst_trace_list_json(){
		$list=$this->cst_trace_list();
		echo json_encode($list);
	}
	
	
}//end class
?>