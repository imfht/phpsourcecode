<?php
/*
 *
 * crm.Website 客户跟踪管理   
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
class CstWebsite extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');
		$this->dict=_instance('Action/crm/CstDict');
		$this->customer=_instance('Action/crm/CstCustomer');
		$this->contract=_instance('Action/crm/SalContract');
	}	
	
	//后台分页列表
	public function cst_website($cusID=0){
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
		$start_date		= $this->_REQUEST("start_date");	
		$end_date		= $this->_REQUEST("end_date");
		$status			= $this->_REQUEST("status");
		
		$where_str 		= " w.customer_id=s.customer_id and s.owner_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";

		if( !empty($keywords) ){
			$where_str .=" and w.name like '%$keywords%'";
		}	
		if( !empty($customer_name) ){
			$where_str .=" and s.name like '%$customer_name%'";
		}	
		//到期时间
		if( !empty($start_date) ){
			switch($start_date){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("-3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("-7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("-15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("-1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("-3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("-6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("-12 month",time()));	
					break;
					
			}
			$where_str .=" and w.start_date>'$date_range'";
		}
		//到期时间
		if( !empty($end_date) ){
			switch($end_date){
				case '3d' :
					$date_range=date('Y-m-d',strtotime("+3 day",time()));
					break;
				case '7d' :
					$date_range=date('Y-m-d',strtotime("+7 day",time()));
					break;
				case '15d' :
					$date_range=date('Y-m-d',strtotime("+15 day",time()));	
					break;
				case '1m' :
					$date_range=date('Y-m-d',strtotime("+1 month",time()));	
					break;
				case '3m' :
					$date_range=date('Y-m-d',strtotime("+3 month",time()));	
					break;
				case '6m' :
					$date_range=date('Y-m-d',strtotime("+6 month",time()));	
					break;
				case '12m' :
					$date_range=date('Y-m-d',strtotime("+12 month",time()));	
					break;
					
			}
			$where_str .=" and w.end_date<'$date_range'";
		}
		if( !empty($status) ){
			$where_str .=" and w.status='$status'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_customer' ){
			$order_by .=" w.customer_id $orderDirection";
		}else if($orderField=='by_startdate'){
			$order_by .=" w.start_date $orderDirection";
		}else if($orderField=='by_enddate'){
			$order_by .=" w.end_date $orderDirection";
		}else{
			$order_by .=" w.website_id desc";
		}
		
		//**************************************************************************
		$countSql    = "select s.name as customer_name ,w.* from cst_website as w,cst_customer as s where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord= ($pageNum-1)*$pageSize;//计算开始行数
		$sql		 = "select s.name as customer_name ,w.* from cst_website as w,cst_customer as s
						where $where_str $order_by limit $beginRecord,$pageSize";		
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['status_item']=$this->cst_website_status($row['status']);
		}
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum);	
		return $assignArray;
		
	}
	public function cst_website_json(){
		$assArr = $this->cst_website();
		echo json_encode($assArr);
	}	
	public function cst_website_show(){
			$assArr = $this->cst_website();
			$smarty = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('crm/cst_website_show.html');	
	}
	
	//网站增加
	public function cst_website_add(){
		$customer_id= $this->_REQUEST("customer_id");
		if(empty($_POST)){
			$customer=$this->customer->cst_customer_list();
			$smarty = $this->setSmarty();
			$smarty->assign(array("customer_id"=>$customer_id,"customer"=>$customer));
			$smarty->display('crm/cst_website_add.html');	
		}else{
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'start_date'=>$this->_REQUEST("start_date"),
				'end_date'=>$this->_REQUEST("end_date"),
				'name'=>$this->_REQUEST("name"),
				'url'=>$this->_REQUEST("url"),
				'icp'=>$this->_REQUEST("icp"),
				'ftp'=>$this->_REQUEST("ftp"),
				'intro'=>$this->_REQUEST("intro"),
				'create_time'=>NOWTIME,
				'create_user_id'=>SYS_USER_ID,
			);
			if($this->C($this->cacheDir)->insert('cst_website',$into_data)){
				$this->L("Common")->ajax_json_success("操作成功");
			}
		}
	}		
	
	//网站续费
	public function cst_website_add_renew(){
			$website_id =$this->_REQUEST("website_id");
			if(empty($_POST)){
				$number =date("ymdh").rand(10,99);
				$website=$this->cst_website_get_one($website_id);
				$smarty = $this->setSmarty();
				$smarty->assign(array("number"=>$number,"one"=>$website));
				$smarty->display('crm/cst_website_add_renew.html');		
			}else{
				$website =$this->cst_website_get_one($website_id);
				$year_num=$this->_REQUEST("year_num");
				$year_num=(int)$year_num;
				$web_end_date=date("Y-m-d", strtotime("+$year_num year", strtotime($website['end_date'])));
				
				//更改服务时间
				$this->C($this->cacheDir)->modify('cst_website',array('end_date'=>$web_end_date),"website_id='$website_id'");
				
				$now_date=date("Y-m-d",time());
				$end_date=date("Y-m-d", strtotime("+$year_num year", strtotime($now_date)));
				$title	 =$website['name']."续费";
				$into_data=array(
					'customer_id'=>$website['customer_id'],
					'start_date'=>$now_date,
					'end_date'=>$end_date,
					'title'=>$title,
					'money'=>$this->_REQUEST("money"),
					'owe_money'=>$this->_REQUEST("money"),
					'our_user_id'=>SYS_USER_ID,
					'intro'=>$this->_REQUEST("intro"),
					'create_time'=>NOWTIME,
					'create_user_id'=>SYS_USER_ID,
				);
				//增加
				$contract_id=$this->C($this->cacheDir)->insert('sal_contract',$into_data);
				//设置订单
				$contract_no=date("Ymd",time())."-".$contract_id;
				$this->C($this->cacheDir)->modify('sal_contract',array('contract_no'=>$contract_no),"contract_id='$contract_id'");
				$this->L("Common")->ajax_json_success("操作成功");		
			}
	}	
	
	public function cst_website_modify(){
		$website_id = $this->_REQUEST("website_id");
		if(empty($_POST)){
			$sql 	= "select * from cst_website where website_id='$website_id'";
			$one 	= $this->C($this->cacheDir)->findOne($sql);	
			$customer=$this->customer->cst_customer_list();
			$smarty  = $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer));
			$smarty->display('crm/cst_website_modify.html');	
		}else{//更新保存数据
			$into_data=array(
				'customer_id'=>$this->_REQUEST("customer_id"),
				'start_date'=>$this->_REQUEST("start_date"),
				'end_date'=>$this->_REQUEST("end_date"),
				'name'=>$this->_REQUEST("name"),
				'url'=>$this->_REQUEST("url"),
				'icp'=>$this->_REQUEST("icp"),
				'ftp'=>$this->_REQUEST("ftp"),
				'intro'=>$this->_REQUEST("intro"),
			);
			if($this->C($this->cacheDir)->modify('cst_website',$into_data,"website_id='$website_id'")){
				$this->L("Common")->ajax_json_success("操作成功");
			}
		}
	}
	
	//关闭维护
	public function cst_website_close(){
		$website_id	 = $this->_REQUEST("website_id");
		$sql  = "update cst_website set status='-1' where website_id in ($website_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	//开启维护
	public function cst_website_open(){
		$website_id	 = $this->_REQUEST("website_id");
		$sql  = "update cst_website set status='1' where website_id in ($website_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
	//查询一条记录
	public function cst_website_get_one($id=""){
		if($id){
			$sql = "select * from cst_website where website_id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}	
	
	public function cst_website_del(){
		$website_id = $this->_REQUEST("website_id");
		$sql = "delete from cst_website where website_id in ($website_id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");	
	}	
	
/*	public function cst_website_select(){
		$cusID  = $this->_REQUEST("cusID");
		$sql	="select id,name from cst_website where cusID='$cusID' order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		echo json_encode($list);
	}	
*/

	//网站状态
	public function cst_website_status($key=null){
		$data=array(
				"1"=>array(
				 		'status_name'=>'维护中',
				 		'status_name_html'=>'<span class="label label-info">维护中<span>',
					),
				"-1"=>array(
							'status_name'=>'已流失',
							'status_name_html'=>'<span class="label">已流失<span>',
					)
		);
		return (array_key_exists($key,$data))?$data[$key]:$data;
	}
					
}//end class
?>