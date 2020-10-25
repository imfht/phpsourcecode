<?php
/*
 * 收票记录类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class FinInvoiceRece extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/Auth');
	}	
	
	public function fin_invoice_rece($id=null){
		$currentPage = $this->_REQUEST("pageNum");//第几页
		$numPerPage  = $this->_REQUEST("numPerPage");//每页多少条
		$currentPage = empty($currentPage)?1:$currentPage;
		$numPerPage  = empty($numPerPage)?$GLOBALS["pageSize"]:$numPerPage;
		
		$posID		 =$this->_REQUEST("posID");
		if($id){
			$where_str  = " and id in($id)";
		}else{
			$where_str  = " id>0";
		}
		if($posID){
			$where_str .=" and posID='$posID'";
		}		
		$countSql    = "select id from fin_invoice_rece where $where_str";
		$totalCount  = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
	
		$moneySql    = "select sum(money) as sum_money from fin_invoice_rece where $where_str";
		$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);
		
		$beginRecord = ($currentPage-1)*$numPerPage;
		$sql		 = "select * from fin_invoice_rece 
						where $where_str order by id desc limit $beginRecord,$numPerPage";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		//供应商
		$supplier= array();
		$posorder= array();
		if(is_array($list)){
			foreach($list as $key=>$row){
				$list[$key]["create_user"]	  = $this->L("User")->user_get_name($row['create_userID']);
				$supplier[$row['id']] = $this->L("Supplier")->supplier_get_name($row['supID']);
				$posorder[$row['id']] = $this->L("PosOrder")->pos_order_get_name($row['posID']);
			}
		}
		$assignArray = array('list'=>$list,'total_money'=>$moneyRs["sum_money"],
								'supplier'=>$supplier,'posorder'=>$posorder,'supplier'=>$supplier,'supplier'=>$supplier,
								"numPerPage"=>$numPerPage,"totalCount"=>$totalCount,"currentPage"=>$currentPage);	
		return $assignArray;
	}
	public function fin_invoice_rece_show(){
		$list	 = $this->fin_invoice_rece();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);//框架变量注入同样适用于smarty的assign方法
		$smarty->display('fin_invoice_rece/fin_invoice_rece_show.html');	
	}		

	public function fin_invoice_rece_show_box(){
		$list	 = $this->fin_invoice_rece();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);
		$smarty->display('fin_invoice_rece/fin_invoice_rece_show_box.html');	
	}	

	public function fin_invoice_rece_add(){
		if(empty($_POST)){
			$smarty  = $this->setSmarty();
			//$smarty->assign();//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_invoice_rece/fin_invoice_rece_add.html');	
		}else{
			$id			=$this->_REQUEST("id");;
			$posID		=$this->_REQUEST("order_id");
			$supID		=$this->_REQUEST("org_id");
			$stages		=$this->_REQUEST("stages");
			$recedate	=$this->_REQUEST("recedate");
			$paymoney	=$this->_REQUEST("pay_money");	
			$billmoney	=$this->_REQUEST("bill_money");
			$name		=$this->_REQUEST("name");
			$invo_number=$this->_REQUEST("invo_number");
			$invo_money	=$this->_REQUEST("order_now_bill_money");//收票金额
			$intro		=$this->_REQUEST("intro");	
			
			$sql= "insert into fin_invoice_rece(posID,supID,recedate,money,stages,invo_number,
												name,intro,adt,create_userID) 
								values('$posID','$supID','$recedate','$invo_money','$stages','$invo_number',
										'$name','$intro','".NOWTIME."','".SYS_USER_ID."');";
			if($this->C($this->cacheDir)->update($sql)>0){
				$this->L("PosOrder")->pos_order_invo_modify($posID,$new_money=$invo_money);
				$this->L("Common")->ajax_json_success("操作成功","2","/FinInvoiceRece/fin_invoice_rece_show/");	
			}
		}
	}	

	//收票
	public function fin_invoice_rece_modify(){
		$id	 = $this->_REQUEST("id");
		if(empty($_POST)){
			$sql 		= "select * from fin_invoice_rece where id='$id'";
			$one 		= $this->C($this->cacheDir)->findOne($sql);	
			$parentID	= $this->fin_invoice_rece_select_tree($one["parentID"]);
			$smarty  	= $this->setSmarty();
			$smarty->assign(array("one"=>$one,"parentID"=>$parentID));//框架变量注入同样适用于smarty的assign方法
			$smarty->display('fin_invoice_rece/fin_invoice_rece_modify.html');	
		}else{
			$sql= "update fin_invoice_rece set name='$_POST[name]',
											 parentID='$_POST[parentID]',sort='$_POST[sort]',
											 visible='$_POST[visible]',intro='$_POST[intro]'
					where id='$id'";
			$this->C($this->cacheDir)->update($sql);	
			$this->L("Common")->ajax_json_success("操作成功","1","/FinInvoiceRece/fin_invoice_rece_show/");			
		}
	}	
	public function fin_invoice_rece_del(){
		$id =$this->_REQUEST("ids");
		$sql="delete from fin_invoice_rece where id in ($id)";
		$this->C($this->cacheDir)->update($sql);	
		$this->L("Common")->ajax_json_success("操作成功","1","/FinInvoiceRece/fin_invoice_rece_show/");	
	}	
	
	public function fin_invoice_rece_table_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$assArr  = $this->fin_invoice_rece();
			$sql	 = "select * from fin_invoice_rece  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			return $tree->get_tree(0, "<tr target='sid_user' rel='\$id'><td> \$sort</td> <td>\$spacer \$name</td> <td> \$intro</td> </tr>", 0, '' , "");
	}	
	public function fin_invoice_rece_select_tree($sid =""){
			$tree	 = _instance('Extend/Tree');
			$sql	 = "select * from fin_invoice_rece  order by sort asc;";	
			$list	 = $this->C($this->cacheDir)->findAll($sql);	
			$tree->tree($list);	
			$parentID  = "<select name=\"parentID\" >";
			$parentID .= "<option value='0' >添加一级分类</option>";
			$parentID .= $tree->get_tree(0, "<option value='\$id' \$selected>\$spacer\$name</option>\n", $sid , '' , "");
			$parentID .="</select>";	
			return $parentID;
	}
	//将数组转化为树形数组
	public function arrToTree($data,$pid){
		$tree = array();
		foreach($data as $k => $v){
			if($v['parentID'] == $pid){
				$v['parentID'] = $this->arrToTree($data,$v['id']);
				$tree[] = $v;
			}
		}        
		return $tree;
	}
	//左边菜单栏输出
	public function outToHtml($tree){
		$html = '';
		foreach($tree as $t){
			if(empty($t['parentID'])){
				$html .= "<li><a href=\"javascript:\" onclick=\"$.bringBack({typeID:'$t[id]',typeName:'$t[name]'})\">$t[name]</a></li>";
			}else{
				$html .='<li><a href="javascript:">'.$t['name'].'</a><ul>';
				$html .= $this->outToHtml($t['parentID']);
				$html  = $html.'</ul></li>';
			}
		} 
		return $html;
	}
	public function fin_invoice_rece_arr(){
		$rtArr  =array();
		$sql	="select id,name from fin_invoice_rece";
		$list	=$this->C($this->cacheDir)->findAll($sql);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$rtArr[$row["id"]]=$row["name"];
			}
		}
		return $rtArr;
	}	
//关联业务选择
	public function fin_invoice_get_supplier_business(){
		$order	=$this->L("PosOrder")->pos_order_select('bill_status');
/*		print_r($order);
		print_r($contr);*/
/*            [id] => 4
            [name] => 100
            [money] => 1000
            [bill_money] => 0
            [zero_money] => 0
            [back_money] => 0
            [now_back_money] => 1000*/
		$rtnArr	=array();
		$key	=0;
		foreach($order as $row){
			$rtnArr[$key]			=$row;
			$rtnArr[$key]["type"]	="pos_order";
			$key++;
		}
		//print_r($rtnArr);
		echo json_encode($rtnArr);
	}
		
}//end class
?>