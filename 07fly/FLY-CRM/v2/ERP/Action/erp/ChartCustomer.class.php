<?php	 
/*
 * epr.ChartCustomer  按销客户总统计
 *
 * @copyright   Copyright (C) 2017-2028 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class ChartCustomer extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {
		_instance('Action/sysmanage/Auth');

	}	
	public function chart_customer(){
		//**获得传送来的数据作分页处理
		$pageNum = $this->_REQUEST("pageNum");//第几页
		$pageSize= $this->_REQUEST("pageSize");//每页多少条
		$pageNum = empty($pageNum)?1:$pageNum;
		$pageSize= empty($pageSize)?'1000':$pageSize;
		
		$keywords	 	= $this->_REQUEST("keywords");
		$start_date_b	= $this->_REQUEST("start_date_b");
		$start_date_e	= $this->_REQUEST("start_date_e");
		$create_date_b	= $this->_REQUEST("create_date_b");		
		$create_date_e	= $this->_REQUEST("create_date_e");		
		$customer_name	= $this->_REQUEST("customer_name");

		$where_str	= " owner_user_id in (".SYS_USER_ID.",".SYS_USER_SUB_ID.")";
		if(!empty($keywords)){
			$where_str	.= " and (order_name like '%$keywords%' or intro like '%$keywords%' or customer_name like '%$keywords%')";
		}
		
		//创建日期
		if( !empty($create_date_b) ){
			$where_str .=" and create_time>='$create_date_b'";
		}		
		if( !empty($create_date_e) ){
			$where_str .=" and create_time<'$create_date_b'";
		}
		//排序操作
		$orderField = $this->_REQUEST("orderField");
		$orderDirection = $this->_REQUEST("orderDirection");
		$order_by="order by";
		if( $orderField=='by_total_money' ){
			$order_by .=" cc.total_money $orderDirection";
		}else if($orderField=='by_total_back_money'){
			$order_by .=" cc.total_back_money $orderDirection";
		}else if($orderField=='by_total_owe_money'){
			$order_by .=" cc.total_owe_money $orderDirection";
		}else if($orderField=='by_total_money_cost'){
			$order_by .=" cc.total_money_cost $orderDirection";
		}else if($orderField=='by_total_pay_money'){
			$order_by .=" cc.total_pay_money $orderDirection";
		}else if($orderField=='by_total_unpaid_money'){
			$order_by .=" cc.total_unpaid_money $orderDirection";
		}else if($orderField=='by_total_total_num'){
			$order_by .=" cc.total_total_num $orderDirection";
		}else if($orderField=='by_total_profit_money'){
			$order_by .=" cc.total_profit_money $orderDirection";
		}else{
			$order_by .=" cc.total_num desc";
		}			
		
		$countSql   = "
				SELECT u.name,cc.* from fly_sys_user as u
				LEFT JOIN 
					(
						SELECT owner_user_id,count(1) as total_num,
						sum(total_money) as c_total_money,sum(total_money_cost) as c_total_money_cost,sum(total_integral) as c_total_integral,
						sum(total_cash) as c_total_cash
						FROM cst_customer
						WHERE $where_str
						GROUP BY owner_user_id 
					) AS cc
				ON cc.owner_user_id=u.id
				$order_by
		";
		
		
		$totalCount	 = $this->C($this->cacheDir)->countRecords($countSql);	//计算记录数
		$beginRecord = ($pageNum-1)*$pageSize;
		
		$sql		 = $countSql." limit $beginRecord,$pageSize";	
		$list		 = $this->C($this->cacheDir)->findAll($sql);
		$moneyRs	 = array('c_total_money'=>0,'c_total_money_cost'=>0,'c_total_integral'=>0,'c_total_cash'=>0, 'total_num'=>0);
		if(is_array($list)){
			foreach($list as $key=>$row){
				$moneyRs['c_total_money'] +=$row['c_total_money'];
				$moneyRs['c_total_money_cost'] +=$row['c_total_money_cost'];
				$moneyRs['c_total_integral'] +=$row['c_total_integral'];
				$moneyRs['c_total_cash'] +=$row['c_total_cash'];
				$moneyRs['total_num'] +=$row['total_num'];
			}
			
		}
		//$moneySql   = "select sum(money) as total_money from chart_sale  where $where_str";
		//$moneyRs	 = $this->C($this->cacheDir)->findOne($moneySql);		
		
		$assignArray = array('list'=>$list,"pageSize"=>$pageSize,"totalCount"=>$totalCount,"pageNum"=>$pageNum,"moneyRs"=>$moneyRs);	
		return $assignArray;
	}
	public function chart_customer_json(){
		$list	 = $this->chart_customer();
		echo json_encode($list);	
	}	
	public function chart_customer_show(){
		$list	 = $this->chart_customer();
		$smarty  = $this->setSmarty();
		$smarty->assign($list);
		$smarty->display('erp/chart_customer_show.html');	
	}	

	
}//
?>