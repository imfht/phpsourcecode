<?php
/*
 * 销售订单类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class SalOrder extends Action{
	private $cacheDir='';//缓存目录	
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function sal_order(){
	
		//**获得传送来的数据作分页处理
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name	  = $this->_REQUEST("cus_name");
		$cusID 		  = $this->_REQUEST("cusID");
		$searchKeyword = $this->_REQUEST("searchKeyword");
		$searchValue  = $this->_REQUEST("searchValue");
		$where_str 	  = " s.cusID=c.id and s.create_userID in (".SYS_USER_VIEW.")";
		if(!empty($cusID) ){
			$where_str .=" and s.cusID='$cusID'";
		}
		if( !empty($searchValue) ){
			$where_str .=" and s.$searchKeyword like '%$searchValue%'";
		}	
		if( !empty($bdt) ){
			$where_str .=" and s.adt >= '$bdt'";
		}			
		if( !empty($edt) ){
			$where_str .=" and s.adt < '$edt'";
		}	
		//**************************************************************************
		
		$moneySql    = "select sum(s.money) as total_money,
								sum(s.back_money) as total_back_money,
								sum(s.zero_money) as total_zero_money,
								sum(s.pay_money) as total_pay_money
						 from sal_order as s,cst_customer as c where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$countSql    = "select s.id from sal_order as s,cst_customer as c where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select s.*,c.name as cst_name from sal_order as s,cst_customer as c
						where $where_str 
						order by s.id desc 
						limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$operate   	= array();
		$money		 = array();
		$status		 = $this->sal_order_status();
		foreach($list as $key=>$row){
			$list[$key]['status_name']	=$status[$row['status']];
			$operate[$row["id"]]=$this->sal_order_operate($row["status"],$row["id"]);
			$money[$row["id"]]	=$this->L("SalOrderDetail")->sal_get_one_order_detail_money($row["id"]);
		}
		
		$count_str	 =" 总金额合计:<font color='red'>".$moneyRs["total_money"]."</font>,";
		$count_str	.=" 回款金额合计:<font color='red'>".$moneyRs["total_back_money"]."</font>,";
		$count_str	.=" 去零金额合计:<font color='red'>".$moneyRs["total_zero_money"]."</font>,";
		$count_str	.=" 发货金额合计:<font color='red'>".$moneyRs["total_pay_money"]."</font>";
		$assignArray =array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage,
								"operate"=>$operate,"count_str"=>$count_str
						);	
		return $assignArray;
		
	}
	//显示订单列表
	public function sal_order_show(){
			$assArr  				= $this->sal_order();
			$assArr["customer"]		= $this->L("Customer")->customer_arr();
			$assArr["linkman"] 		= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 		= $this->sal_order_status();
			$assArr["pay_status"] 	= $this->sal_order_pay_status();
			$assArr["deliver_status"] = $this->sal_order_deliver_status();
			$assArr["bill_status"] 	= $this->sal_order_bill_status();
			$assArr["chance"] 		= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"]		= $this->L("User")->user_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_order/sal_order_show.html');	
	}	
	
	//显示订单列表
	public function sal_order_show_box(){
			$assArr  				= $this->sal_order();
			$assArr["customer"]		= $this->L("Customer")->customer_arr();
			$assArr["linkman"] 		= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 		= $this->sal_order_status();
			$assArr["pay_status"] 	= $this->sal_order_pay_status();
			$assArr["deliver_status"] = $this->sal_order_deliver_status();
			$assArr["bill_status"] 	= $this->sal_order_bill_status();
			$assArr["chance"] 		= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"]		= $this->L("User")->user_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_order/sal_order_show_box.html');	
	}	

	//查看订单详细
	public function sal_order_show_one(){
			$id		    = $this->_REQUEST("id");
			$sql 		= "select * from sal_order where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users		= $this->L("User")->user_arr();
			$status 	= $this->sal_order_status();
			$pay_status	= $this->sal_order_pay_status();
			$deliver_status = $this->sal_order_deliver_status();
			$bill_status= $this->sal_order_bill_status();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,
									"customer"=>$customer,
									"linkman"=>$linkman,
									"dict"=>$dict,
									"chance"=>$chance,
									"status"=>$status,
									"pay_status"=>$pay_status,
									"deliver_status"=>$deliver_status,
									"bill_status"=>$bill_status,
									"users"=>$users
							));
			$smarty->display('sal_order/sal_order_show_one.html');		
	}	
	
	public function sal_order_add(){
		if(empty($_POST)){
			$number = date("ymdH").rand(10,99);
			$smarty = $this->setSmarty();
			$smarty->assign(array("number"=>$number));
			$smarty->display('sal_order/sal_order_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$cusID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$our_userID	= $this->_REQUEST("our_id");
			$sql       	= "insert into sal_order(ord_number,cusID,linkmanID,chanceID,our_userID,
											bdt,edt,title,intro,adt,create_userID) 
								values(
									'$_POST[ord_number]','$cusID','$linkmanID','$chanceID','$our_userID',
									'$_POST[bdt]','$_POST[edt]','$_POST[title]','$_POST[intro]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	public function sal_order_get_one($id=""){
		if($id){
			$sql = "select * from sal_order where id='$id'";
			$one = $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}	
	
	public function sal_order_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from sal_order where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users		= $this->L("User")->user_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance,"users"=>$users));
			$smarty->display('sal_order/sal_order_modify.html');
				
		}else{//更新保存数据
		
			$cusID   	 = $this->_REQUEST("org_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$sql= "update sal_order set 
							ord_number='$_POST[ord_number]',
							money='$_POST[money]',
							cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',
							bdt='$_POST[bdt]',edt='$_POST[edt]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function sal_order_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from sal_order where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/SalOrder/sal_order_show/");	
	}


	//下拉选择回放数据
	public function sal_order_select($type=null){
		$cusID  = $this->_REQUEST("cusID");
		switch($type){
			case "pay_status":
				$where_str="and pay_status in(1,2)";
				break;
			case "bill_status":
				$where_str="and bill_status in(1,2)";
				break;
			default:
		}
		$sql	= "select id,title as name,money,bill_money,zero_money,back_money,(money-zero_money-back_money) as now_back_money,(money-zero_money-bill_money) as now_bill_money from sal_order where cusID='$cusID' $where_str order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
	
		
	//审核
	public function sal_order_audit(){
		$id	  	  = $this->_REQUEST("id");
		$status	  = $this->_REQUEST("status");
		$sql= "update sal_order set status='$status' where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");				
	}
	
	//订单状态
	public function sal_order_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>临时单</b>",
				"2"=>"<b style='color:#0000FF'>执行中</b>",
				"3"=>"<b style='color:#008000'>完成</b>",
				"4"=>"<b style='color:#ff0000'>撤销</b>"
		);
	}

	//付款状态
	public function sal_order_pay_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>未付</b>",
				"2"=>"<b style='color:#FF0000'>部分</b>",
				"3"=>"<b style='color:#8A2BE2'>已付</b>"
		);
	}
	//库存状态
	public function sal_order_deliver_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#FF0000'>待出库</b>",
				"3"=>"<b style='color:#8A2BE2'>待发货</b>",
				"4"=>"<b style='color:#0000FF'>部分</b>",
				"5"=>"<b style='color:#008000'>全部</b>"
		);
	}
	//发票状态
	public function sal_order_bill_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#008000'>部分</b>",
				"3"=>"<b style='color:#008000'>全部</b>"
		);
	}
	//操作按钮
	public function sal_order_operate($status,$id){
		switch($status){
			case 1:
				$str="<a href='".ACT."/SalOrderDetail/sal_order_detail_add/id/$id/' target='navTab' rel='sal_order_detail_add' title='编辑订单明细' >订单明细</a>";
				break;
			case 2:
				$str="<a href='".ACT."/SalOrder/sal_order_show/id/$id' target='ajaxTodo' title='确定要生成出库单吗?'>生成出库单</a>";
				break;		
			case 3:
				$str="<a href='#'></a>";
				break;				
		}
		return $str;
	}

	//传入ID返回名字
	public function sal_order_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,title as name from sal_order where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}


	//付款修改订单功能
	public function sal_order_pay_modify($cusID,$new_money){
		$one		=$this->sal_order_get_one($cusID);
		$money		=$one["money"];
		$back_money	=$one["back_money"];
		if(($back_money+$new_money)>=$money){
			$pay_status=3;//已付
		}else{
			$pay_status=2;//未付
		}
		//更新回款金额
		$sql="update sal_order set 
					status=2,
					pay_status='$pay_status',
					back_money=back_money+'$new_money' 
				where id='$cusID';";
		if($this->C($this->cacheDir)->update($sql)>0){
			return true;
		}else{
			return false;	
		}
	}

	//收票修改订单功能
	public function sal_order_invo_modify($salID,$new_money){
		$one		=$this->sal_order_get_one($salID);
		$money		=$one["money"];
		$bill_money	=$one["bill_money"];
		if(($bill_money+$new_money)>=$money){
			$bill_status=3;//已付
		}else{
			$bill_status=2;//部分
		}
		//更改付款金额
		$sql="update sal_order set bill_status='$bill_status',
								   bill_money=bill_money+'$new_money' 
			  where id='$salID';";
		if($this->C($this->cacheDir)->update($sql)>0){
			return true;
		}else{
			return false;	
		}
	}

			
}
?>