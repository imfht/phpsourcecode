<?php
/*
 * 合同管理类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class SalContract extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
		//$this->SalContractDetail  = _instance('Action/SalContractDetail');
	}	
	
	public function sal_contract(){
	
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
		
		$where_str = " s.cusID=c.id and s.create_userID in ('".SYS_USER_VIEW."')";
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
						 from sal_contract as s,cst_customer as c where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$countSql   = "select s.id from sal_contract as s,cst_customer as c where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select s.*,c.name as cst_name from sal_contract as s,cst_customer as c
						where $where_str 
						order by s.id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$status		 =$this->sal_contract_status();
		$operate	 = array();
		foreach($list as $key=>$row){
			$operate[$row["id"]]		=$this->sal_contract_operate($row["status"],$row["id"]);
			$list[$key]['status_name']	=$status[$row['status']];
			//$money[$row["id"]]=_instance('Action/SalContractDetail')->cst_get_one_quoted_detail_money($row["id"]);
		}

		$count_str	 =" 总金额合计:<font color='red'>".$moneyRs["total_money"]."</font>,";
		$count_str	.=" 回款金额合计:<font color='red'>".$moneyRs["total_back_money"]."</font>,";
		$count_str	.=" 去零金额合计:<font color='red'>".$moneyRs["total_zero_money"]."</font>,";
		$count_str	.=" 交付金额合计:<font color='red'>".$moneyRs["total_pay_money"]."</font>";
		$assignArray = array('list'=>$list,"numPerPage"=>$numPerPage,
								"totalCount"=>$totalCount,"currentPage"=>$currentPage,
								"operate"=>$operate,"count_str"=>$count_str
						);	
		return $assignArray;
		
	}
	
	//合同显示
	public function sal_contract_show(){
			$assArr  					= $this->sal_contract();
			$assArr["customer"]			= $this->L("Customer")->customer_arr();
			$assArr["dict"] 			= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 			= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 			= $this->sal_contract_status();
			$assArr["renew_status"] 	= $this->sal_contract_renew_status();
			$assArr["pay_status"] 		= $this->sal_contract_pay_status();
			$assArr["deliver_status"] 	= $this->sal_contract_deliver_status();
			$assArr["bill_status"] 		= $this->sal_contract_bill_status();
			$assArr["chance"] 			= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"]			= $this->L("User")->user_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_contract/sal_contract_show.html');	
	}
	
	//合同显示
	public function sal_contract_show_box(){
			$assArr  					= $this->sal_contract();
			$assArr["customer"]			= $this->L("Customer")->customer_arr();
			$assArr["dict"] 			= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 			= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 			= $this->sal_contract_status();
			$assArr["renew_status"] 	= $this->sal_contract_renew_status();
			$assArr["pay_status"] 		= $this->sal_contract_pay_status();
			$assArr["deliver_status"] 	= $this->sal_contract_deliver_status();
			$assArr["bill_status"] 		= $this->sal_contract_bill_status();
			$assArr["chance"] 			= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"]			= $this->L("User")->user_arr();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('sal_contract/sal_contract_show_box.html');	
	}	
	
	//查看一条合同详细
	public function sal_contract_show_one(){
			$id		    = $this->_REQUEST("id");
			$sql 		= "select * from sal_contract where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$users		= $this->L("User")->user_arr();
			$status 	= $this->sal_contract_status();
			$pay_status	= $this->sal_contract_pay_status();
			$deliver_status = $this->sal_contract_deliver_status();
			$bill_status= $this->sal_contract_bill_status();
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
			$smarty->display('sal_contract/sal_contract_show_one.html');		
	}	
			
	
	public function sal_contract_add(){
		if(empty($_POST)){
			$number = date("ymdH").rand(10,99);
			$smarty = $this->setSmarty();
			$smarty->assign(array("number"=>$number));
			$smarty->display('sal_contract/sal_contract_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$cusID   	= $this->_REQUEST("org_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$our_userID	= $this->_REQUEST("our_id");
			$renew_status= $this->_REQUEST("renew_status");
			$sql  = "insert into sal_contract(con_number,money,cusID,linkmanID,chanceID,
							renew_status,
							our_userID,bdt,edt,title,intro,adt,create_userID) 
								values('$_POST[con_number]','$_POST[money]','$cusID','$linkmanID','$chanceID',
								'$_POST[renew_status]',
								'$our_userID','$_POST[bdt]','$_POST[edt]','$_POST[title]','$_POST[intro]','$dt','".SYS_USER_ID."');";
			
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}	
	
	function sal_contract_add_save($data){
			$adt     	= date("Y-m-d H:i:s",time());
			$con_number = $data["con_number"];
			$cusID   	= $data["cusID"];
			$money 		= $data["money"];
			$linkmanID  = $data["linkmanID"];
			$chanceID   = $data["chanceID"];
			$our_userID	= $data["our_userID"];
			$renew_status= $data["renew_status"];
			$websiteID  = $data["websiteID"];
			$bdt        = $data["bdt"];
			$edt        = $data["edt"];
			$title      = $data["title"];
			$intro      = $data["intro"];
			$sql       	= "insert into sal_contract(con_number,money,cusID,linkmanID,chanceID,
							renew_status,websiteID,
							our_userID,bdt,edt,title,intro,adt,create_userID) 
								values('$con_number','$money','$cusID','$linkmanID','$chanceID',
								'$renew_status','$websiteID',
								'$our_userID','$bdt','$edt','$title','$intro','$adt','".SYS_USER_ID."');";
			if($this->C($this->cacheDir)->update($sql)>0){
				return true;
			}else{
				return false;	
			}	
	}
	//查询一条记录
	public function sal_contract_get_one($id=""){
		if($id){
			$sql 		= "select * from sal_contract where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			return $one;
		}	
	}
	//修改
	public function sal_contract_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from sal_contract where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance));
			$smarty->display('sal_contract/sal_contract_modify.html');
				
		}else{//更新保存数据
		
			$cusID   	 = $this->_REQUEST("org_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID    = $this->_REQUEST("chance_id");
			$sql= "update sal_contract set 
							con_number='$_POST[con_number]',
							money='$_POST[money]',
							cusID='$cusID',linkmanID='$linkmanID',chanceID='$chanceID',
							bdt='$_POST[bdt]',edt='$_POST[edt]',
							title='$_POST[title]',intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function sal_contract_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from sal_contract where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/SalContract/sal_contract_show/");	
	}

	//下拉选择回放数据
	public function sal_contract_select($type=null){
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
		$sql	= "select id,title as name,money,bill_money,zero_money,back_money,(money-zero_money-back_money) as now_back_money,(money-zero_money-bill_money) as now_bill_money from sal_contract where cusID='$cusID' $where_str order by id asc;";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		return $list;
	}
		
		
	public function sal_contract_audit(){
		$id	  	  = $this->_REQUEST("id");
		$status	  = $this->_REQUEST("status");
		$sql= "update sal_contract set status='$status' where id='$id'";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功");				
	}

	//传入ID返回名字
	public function sal_contract_get_name($id){
		if(empty($id)) $id=0;
		$sql  ="select id,title as name from sal_contract where id in ($id)";	
		$list =$this->C($this->cacheDir)->findAll($sql);
		$str  ="";
		if(is_array($list)){
			foreach($list as $row){
				$str .= "|-".$row["name"]."&nbsp;";
			}
		}
		return $str;
	}
		
	//合同状态
	public function sal_contract_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>临时单</b>",
				"2"=>"<b style='color:#0000FF'>执行中</b>",
				"3"=>"<b style='color:#008000'>完成</b>",
				"4"=>"<b style='color:#ff0000'>撤销</b>"
		);
	}

	//合同续费状态
	public function sal_contract_renew_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>新增</b>",
				"2"=>"<b style='color:#0000FF'>续费</b>",
				"3"=>"<b style='color:#008000'>流失</b>"
		);
	}

	//付款状态
	public function sal_contract_pay_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>未付</b>",
				"2"=>"<b style='color:#0000FF'>部分</b>",
				"3"=>"<b style='color:#008000'>已付</b>"
		);
	}
	
	//开票状态
	public function sal_contract_deliver_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#0000FF'>部分</b>",
				"3"=>"<b style='color:#008000'>全部</b>"
		);
	}

	//交付状态
	public function sal_contract_bill_status(){
		return  array(
				"1"=>"<b style='color:#FFA500'>需要</b>",
				"2"=>"<b style='color:#008000'>部分</b>",
				"3"=>"<b style='color:#008000'>全部</b>"
		);
	}
	
	//根据不同状态显示操作按钮
	public function sal_contract_operate($status,$id){
		switch($status){
			case 1:
				$str="<a href='".ACT."/SalContractDetail/sal_contract_detail_add/id/$id/' target='navTab' rel='sal_contract_detail_add' title='产品报价明细' >编辑明细</a>
					  <a href='".ACT."/SalContract/sal_contract_audit/status/2/id/$id/' target='ajaxTodo' title='确定要同意吗?'>同意</a>
					  <a href='".ACT."/SalContract/sal_contract_audit/status/3/id/$id/' target='ajaxTodo' title='确定要拒决吗?'>拒决</a>";
				break;
			case 2:
				$str="<a href='".ACT."/SalContract/sal_contract_show/id/$id' target='ajaxTodo' title='确定要生成订单吗?'>生成订单</a>";
				break;		
			case 3:
				$str="<a href='#'></a>";
				break;				
		}
		return $str;
	}
	
	//付款修改合同付付款状态功能
	public function sal_contract_pay_modify($cusID,$new_money){
		$one		=$this->sal_contract_get_one($cusID);
		$money		=$one["money"];
		$back_money	=$one["back_money"];
		if(($back_money+$new_money)>=$money){
			$pay_status=3;//已付
		}else{
			$pay_status=2;//未付
		}
		//更新回款金额
		$sql="update sal_contract set 
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
	public function sal_contract_invo_modify($salID,$new_money){
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