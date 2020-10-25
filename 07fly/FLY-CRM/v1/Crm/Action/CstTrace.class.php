<?php
/*
 * 客户跟踪记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class CstTrace extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	public function cst_trace(){
		//**获得传送来的数据作分页处理
		$currentPage 	= $this->_REQUEST("pageNum");//第几页
		$numPerPage		= $this->_REQUEST("numPerPage");//每页多少条
		$currentPage 	= empty($currentPage)?1:$currentPage;
		$numPerPage  	= empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		$orderField 	= $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		
		//**************************************************************************
		//**获得传送来的数据做条件来查询
		$cus_name		= $this->_REQUEST("cus_name");
		$searchKeyword	= $this->_REQUEST("searchKeyword");
		$searchValue	= $this->_REQUEST("searchValue");
		$cusID 			= $this->_REQUEST("cusID");
		$cus_name   	= $this->_REQUEST("cus_name");
		$where_str = " t.create_userID in (".SYS_USER_VIEW.") ";
		if(!empty($cusID) ){
			$where_str .=" and t.cusID='$cusID'";
		}
		if( !empty($searchValue) ){
			$where_str .=" and t.$searchKeyword like '%$searchValue%'";
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
		
		$order_by="order by";
		if( $orderField=='by_nextbdt' ){
			$order_by .=" t.nextbdt $orderDirection";
		}else if($orderField=='by_bdt'){
			$order_by .=" t.bdt $orderDirection";
		}else{
			$order_by .=" t.id desc";
		}
		//**************************************************************************
		$countSql   = "select c.name as cst_name ,t.* from cst_trace as t 
						left join cst_customer as c on t.cusID=c.id
						where $where_str";
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select c.name as cst_name ,t.* from cst_trace as t
						left join cst_customer as c on t.cusID=c.id
						where $where_str $order_by limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		foreach($list as $key=>$row){
			$list[$key]['salestage_name']	=$this->L("CstDict")->cst_dict_get_name($row['salestage']);
			$list[$key]['salemode_name']	=$this->L("CstDict")->cst_dict_get_name($row['salemode']);
			$list[$key]['linkman_name']	 	=$this->L("CstLinkman")->cst_linkman_get_name($row['linkmanID']);
			$list[$key]['chance_name']	 	=$this->L("CstChance")->cst_chance_get_name($row['chanceID']);
			$list[$key]['create_user_name']	=$this->L("User")->user_get_name($row['create_userID']);
		}
		$assignArray = array('list'=>$list,'cusID'=>$cusID,'cus_name'=>$cus_name,
								"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage,
							 	'orderField'=>$orderField,'orderDirection'=>$orderDirection
						);	
		return $assignArray;
		
	}
	
	public function cst_trace_show(){
			$assArr  = $this->cst_trace();
			$smarty  = $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_trace/cst_trace_show.html');	
	}		

	public function cst_trace_show_box(){
			$assArr  			= $this->cst_trace();
			$assArr["dict"] 	= $this->L("CstDict")->cst_dict_arr();
			$assArr["linkman"] 	= $this->L("CstLinkman")->cst_linkman_arr();
			$assArr["status"] 	= $this->cst_trace_status();
			$assArr["chance"] 	= $this->L("CstChance")->cst_chance_arr();
			$assArr["users"] 	= $this->L("User")->user_arr();
			$smarty  			= $this->setSmarty();
			$smarty->assign($assArr);
			$smarty->display('cst_trace/cst_trace_show_box.html');	
	}	
	
	public function cst_trace_add(){
		$cusID 		= $this->_REQUEST("cusID");
		$cus_name 	= $this->_REQUEST("cus_name");
		if(empty($_POST)){
			$status = $this->cst_trace_status_select("status");
			if($cusID>0){ 
				$cus_name=$this->L("Customer")->customer_get_name($cusID);
			}
			$smarty = $this->setSmarty();
			$smarty->assign(array("status"=>$status,"cusID"=>$cusID,"cus_name"=>$cus_name));
			$smarty->display('cst_trace/cst_trace_add.html');	
		}else{
			$dt	     	= date("Y-m-d H:i:s",time());
			$cusID   	= $this->_REQUEST("org_id");
			$salestage  = $this->_REQUEST("salestage_id");
			$salemode   = $this->_REQUEST("salemode_id");
			$linkmanID  = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			$nextbdt   	= $this->_REQUEST("nextbdt");
			$nexttitle 	 = $this->_REQUEST("nexttitle");	
			$sql       	= "insert into cst_trace(cusID,salestage,salemode,linkmanID,chanceID,nextbdt,nexttitle,
												bdt,status,title,intro,adt,create_userID) 
								values('$cusID','$salestage','$salemode','$linkmanID','$chanceID','$nextbdt','$nexttitle',
								'$_POST[bdt]','$_POST[status]','$_POST[title]','$_POST[intro]','$dt','".SYS_USER_ID."');";
			$this->C($this->cacheDir)->update($sql);	
			
					
			/*//当计划下次沟通时，增加一条计划沟通计划status=1
			if(!empty($nextbdt) && !empty($nexttitle)){
				$sql= "insert into cst_trace(cusID,salestage,salemode,linkmanID,chanceID,
												bdt,status,title,intro,adt) 
								values('$cusID','$salestage','$salemode','$linkmanID','$chanceID',
								'$_POST[nextbdt]','1','$_POST[nexttitle]','$_POST[intro]','$dt');";	
				$this->C($this->cacheDir)->update($sql);								
			}*/
			$this->L("Common")->ajax_json_success("操作成功");		
		}
	}		
	
	
	public function cst_trace_modify(){
		$id	  	 = $this->_REQUEST("id");
		$dt	     = date("Y-m-d H:i:s",time());
		if(empty($_POST)){
			$sql 		= "select * from cst_trace where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$customer   = $this->L("Customer")->customer_arr();
			$linkman    = $this->L("CstLinkman")->cst_linkman_arr();
			$dict		= $this->L("CstDict")->cst_dict_arr();
			$chance		= $this->L("CstChance")->cst_chance_arr();
			$status 	= $this->cst_trace_status_select("status",$one["status"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"customer"=>$customer,"linkman"=>$linkman,"dict"=>$dict,"chance"=>$chance,"status"=>$status));
			$smarty->display('cst_trace/cst_trace_modify.html');	
		}else{//更新保存数据
		
			$cusID   = $this->_REQUEST("org_id");
			$salestage   = $this->_REQUEST("salestage_id");
			$salemode    = $this->_REQUEST("salemode_id");
			$linkmanID   = $this->_REQUEST("linkman_id");
			$chanceID   = $this->_REQUEST("chance_id");
			
			$nextbdt   = $this->_REQUEST("nextbdt");
			$nexttitle = $this->_REQUEST("nexttitle");
			$sql= "update cst_trace set 
							cusID='$cusID',
							linkmanID='$linkmanID',
							salestage='$salestage',
							salemode='$salemode',
							chanceID='$chanceID',
							bdt='$_POST[bdt]'
							,status='$_POST[status]',
							title='$_POST[title]',
							intro='$_POST[intro]'
			 		where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			
			//当计划下次沟通时，增加一条计划沟通计划status=1
			if(!empty($nextbdt) && !empty($nexttitle)){
				$sql= "insert into cst_trace(cusID,salestage,salemode,linkmanID,chanceID,
												bdt,status,title,intro,adt) 
								values('$cusID','$salestage','$salemode','$linkmanID',$chanceID,
								'$_POST[nextbdt]','1','$_POST[nexttitle]','$_POST[intro]','$dt');";	
				$this->C($this->cacheDir)->update($sql);								
			}
			
			
			$this->L("Common")->ajax_json_success("操作成功");			
		}
	}
	
		
	public function cst_trace_del(){
		$id	  = $this->_REQUEST("ids");
		$sql  = "delete from cst_trace where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/CstTrace/cst_trace_show/");	
	}	
	public function cst_trace_status(){
		return array("1"=>"<b style='color:#FFA500'>计划联系</b>","2"=>"<b style='color:#008000'>已经联系</b>");
	}
	public function cst_trace_status_select($inputname,$value=""){
		$data=$this->cst_trace_status();
		$string ="<select name='$inputname'>";
		foreach($data as $key=>$va){
			$string.="<option value='$key'";
			if($key==$value) $string.=" selected";
			$string.=">".$va."</option>";
		}
		$string.="</select>";
		return $string;
	}		
	public function cst_trace_get_last_one($cusID=""){
			if($cusID){
			  $sql = "select * from cst_trace
					  where cusID='$cusID' order by id desc limit 0,1;
					  ";
			  $one = $this->C($this->cacheDir)->findOne($sql);	
			  return $one;
			}	
	}			
}//end class
?>