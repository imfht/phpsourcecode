<?php
/*
 * 采购订单类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class PosOrder extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function pos_order(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	   	   = $this->_REQUEST("cus_name");
		$searchKeyword	   = $this->_REQUEST("searchKeyword");
		$searchValue	   = $this->_REQUEST("searchValue");
		$where_str 		   = " create_userID in (".SYS_USER_VIEW.")";

		if( !empty($searchValue) ){
			$where_str .=" and $searchKeyword like '%$searchValue%'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and adt < '$edt'";
		}	
		//**************************************************************************
		$countSql    = "select id from pos_order where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from pos_order where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$money = "";
		foreach($list as $key=>$row){
			$operate[$row["id"]]=$this->pos_order_operate($row["status"],$row["id"]);
			//$money[$row["id"]]= $this->L("PosOrderDetail")->sal_get_one_order_detail_money($row["id"]);
		}
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage,
								"operate"=>$operate,"money"=>$money
						);	
		return $assignArray;
		
	}
	
	public function pos_order_show(){
			$assArr  				= $this->pos_order();
			$assArr["supplier"]		= $this->L("Supplier")->supplier_arr();
			$assArr["linkman"] 		= $this->L("SupLinkman")->sup_linkman_arr();
			$assArr["status"] 		= $this->pos_order_status();
			$assArr["pay_status"] 	= $this->pos_order_pay_status();
			$assArr["into_status"]  = $this->pos_order_into_status();
			$assArr["bill_status"] 	= $this->pos_order_bill_status();
			$assArr["chance"] 		= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"]		= $this->L("User")->user_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('pos_order/pos_order_show.html');	
	}		
	
	
	//查看订单详细
	public function pos_order_show_one(){
			$id		    		= $this->_REQUEST("id");
			$sql 				= "select * from pos_order where id='$id'";
			$one 				= $this->C($this->cacheDir)->findOne($sql);	
			$one["sup_name"]  	= $this->L("Supplier")->supplier_get_name($one["supID"]);
			$linkman    		= $this->L("SupLinkman")->sup_linkman_arr();
			$users				= $this->L("User")->user_arr();
			$status 			= $this->pos_order_status();
			$pay_status 		= $this->pos_order_pay_status();
			$into_status  		= $this->pos_order_into_status();
			$bill_status 		= $this->pos_order_bill_status();

			$smarty  			= $this->setSmarty();
			$smarty->assign(array("one"=>$one,
								"linkman"=>$linkman,
								"users"=>$users,
								"status"=>$status,
								"pay_status"=>$pay_status,
								"into_status"=>$into_status,
								"bill_status"=>$bill_status,
								));
			$smarty->display('pos_order/pos_order_show_one.html');
			
	}
	
	public function pos_order_add(){
		if(empty($_POST)){
			$number = date("ymdH").rand(10,99);
			$smarty = $this->setSmarty();
			$smarty->assign(array("number"=>$number));
			$smarty->display('pos_order/pos_order_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$supID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$our_userID	= $this->_REQUEST("our_id");
			$sql       	= "insert into pos_order(
									pos_number,money,zero_money,pay_money,supID,linkmanID,our_userID,
									bdt,edt,title,intro,adt,create_userID) 
								values(
									'$_POST[pos_number]','$_POST[money]','$_POST[zero_money]','$_POST[pay_money]','$supID','$linkmanID','$our_userID',
									'$_POST[bdt]','$_POST[edt]','$_POST[title]','$_POST[intro]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}
			
	public function pos_order_get_one($id=""){
		if($id){
			$sql = "select * from pos_order where id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}	
	
	public function pos_order_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 				= "select * from pos_order where id='$id'";
			$one 				= $this->C($this->cacheDir)->findOne($sql);	
			$one["sup_name"]  	= $this->L("Supplier")->supplier_get_name($one["supID"]);
			$linkman    		= $this->L("SupLinkman")->sup_linkman_arr();
			$users				= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign(array("one"=>$one,
								"linkman"=>$linkman,
								"users"=>$users));
			$smarty->display('pos_order/pos_order_modify.html');
				
		}else{//更新保存数据
		
			$supID   	 = $this->_REQUEST("sup_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$our_userID  = $this->_REQUEST("our_id");
			$sql= "update pos_order set 
							money='$_POST[money]',
							supID='$supID',
							linkmanID='$linkmanID',
							our_userID='$our_userID',
							bdt='$_POST[bdt]',
							edt='$_POST[edt]',
							title='$_POST[title]',
							intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
				$this->L("Common")->ajax_json_success("操作成功","2","/PosOrder/pos_order_show/");		
		}
	}
	
		
	public function pos_order_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from pos_order where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/PosOrder/pos_order_show/");	
	}
	
	//订单审核
	public function pos_order_audit(){
		$id	  	  = $this->_REQUEST("id");
		$status	  = $this->_REQUEST("status");
		$sql= "update pos_order set status='$status' where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");				
	}
	
	//订单状态
	public function pos_order_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>临时单</b>",
				"2"=>"<b style='color:#0000FF'>执行中</b>",
				"2"=>"<b style='color:#008000'>完成</b>",
				"4"=>"<b style='color:#ff0000'>撤销</b>"
		);
	}
	
	//付款状态
	public function pos_order_pay_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>未付</b>",
				"2"=>"<b style='color:#FF0000'>部分</b>",
				"3"=>"<b style='color:#8A2BE2'>已付</b>"
		);
	}
	
	//放库状态
	public function pos_order_into_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#FF0000'>已录明细</b>",
				"3"=>"<b style='color:#8A2BE2'>待入库</b>",
				"4"=>"<b style='color:#0000FF'>部分</b>",
				"5"=>"<b style='color:#008000'>全部</b>"
		);
	}
	
	//发票状态
	public function pos_order_bill_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#008000'>部分</b>",
				"3"=>"<b style='color:#008000'>已开</b>"
		);
	}
	
	public function pos_order_operate($status,$id){
		switch($status){
			case 1:
				$str="<a href='".ACT."/PosOrderDetail/pos_order_detail_add/id/$id/' target='navTab' rel='pos_order_detail_add' title='编辑订单明细' >订单明细</a>";
				break;
			case 2:
				$str="<a href='".ACT."/PosOrder/pos_order_show/id/$id' target='ajaxTodo' title='确定要生成订单吗?'>生成订单</a>";
				break;		
			case 3:
				$str="<a href='#'></a>";
				break;				
		}
		return $str;
	}	
	
	//付款修改订单功能
	public function pos_order_pay_modify($posID,$new_money){
		$one		=$this->pos_order_get_one($posID);
		$money		=$one["money"];
		$pay_money	=$one["pay_money"];
		if(($pay_money+$new_money)>=$money){
			$pay_status=3;//已付
		}else{
			$pay_status=2;//未付
		}
		//更改付款金额
		$sql="update pos_order set pay_status='$pay_status',pay_money=pay_money+'$new_money' where id='$posID';";
		if($this->C($this->cacheDir)->update($sql)>0){
			return true;
		}else{
			return false;	
		}
	}

	//收票修改订单功能
	public function pos_order_invo_modify($posID,$new_money){
		$one		=$this->pos_order_get_one($posID);
		$money		=$one["money"];
		$bill_money	=$one["bill_money"];
		if(($bill_money+$new_money)>=$money){
			$bill_status=3;//已付
		}else{
			$bill_status=2;//部分
		}
		//更改付款金额
		$sql="update pos_order set bill_status='$bill_status',bill_money=bill_money+'$new_money' where id='$posID';";
		if($this->C($this->cacheDir)->update($sql)>0){
			return true;
		}else{
			return false;	
		}
	}	
	
	//下拉选择回放数据
	public function pos_order_select($type=null){
		$supID  = $this->_REQUEST("supID");
		switch($type){
			case "pay_status":
				$where_str="and pay_status in(1,2)";
				break;
			case "bill_status":
				$where_str="and bill_status in(1,2)";
				break;
			default:
		}
		$sql	= "select id,title as name,money,bill_money,zero_money,pay_money,(money-zero_money-pay_money) as now_pay_money,(money-bill_money) as now_bill_money from pos_order where supID='$supID' {$where_str} order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}

	//传入ID返回名字
	public function pos_order_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,title as name from pos_order where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
		
}// end class
?>