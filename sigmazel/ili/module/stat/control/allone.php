<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//总体
class allone{
	//默认
	public function index(){
		global $_var, $db;
		
		$search = array('querystring' => '', 'wheresql' => '', 'begindate' => '', 'enddate' => '');
		
		if($_var['gp_txtBeginDate']) {
			$search['querystring'] .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$search['begindate'] = date('Y-m-d', strtotime($_var['gp_txtBeginDate']));
		}
		
		if($_var['gp_txtEndDate']) {
			$search['querystring'] .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$search['enddate'] = date('Y-m-d', strtotime($_var['gp_txtEndDate']));
		}
		
		//浏览量
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND DATELINE >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND DATELINE < '{$search[enddate]}'";
		
		$viewstat = $db->fetch_first("SELECT COUNT(1) AS DATECOUNT, SUM(VIEWS) AS VIEWS FROM (SELECT DATE_FORMAT(DATELINE, '%Y-%m-%d') AS DATELINE, SUM(VIEWS) AS VIEWS FROM tbl_view_hour WHERE 1 {$search[wheresql]} GROUP BY DATE_FORMAT(DATELINE, '%Y-%m-%d')) as temp");
		if($viewstat) $viewstat['AVG_VIEWS'] = round($viewstat['VIEWS'] / $viewstat['DATECOUNT'], 0);
		
		
		//用户数
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND CREATETIME < '{$search[enddate]}'";
		
		$userstat = $db->fetch_first("SELECT COUNT(1) AS DATECOUNT, SUM(USERS) AS USERS FROM (SELECT DATE_FORMAT(CREATETIME, '%Y-%m') AS DATELINE, COUNT(1) AS USERS FROM tbl_user WHERE ISMANAGER = 0 {$search[wheresql]} GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m')) as temp");
		if($userstat) $userstat['AVG_USERS'] = round($userstat['USERS'] / $userstat['DATECOUNT'], 0);
		
		//商品数
		$productstat['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_product");
		$productstat['ISAUDIT'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE ISAUDIT = 1");
		$productstat['SELLING'] = $db->result_first("SELECT COUNT(1) FROM tbl_product WHERE SELLING = 1");
		
		//品牌数
		$brandstat['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_brand");
		$brandstat['ISAUDIT'] = $db->result_first("SELECT COUNT(1) FROM tbl_brand WHERE ISAUDIT = 1");
		
		
		//订单数
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND CREATETIME < '{$search[enddate]}'";
		
		$orderstat['ALL'] = $db->result_first("SELECT COUNT(1) FROM tbl_order");
		$orderstat['STATUS'] = $db->result_first("SELECT COUNT(1) FROM tbl_order WHERE STATUS > 0 {$search[wheresql]}");
		
		//销售总额数
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND CREATETIME < '{$search[enddate]}'";
		
		$salestat['ALL'] = $db->result_first("SELECT SUM(TOTALPRICE) FROM tbl_order WHERE PAY = 1 {$search[wheresql]}");
		
		//月订单数
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND CREATETIME < '{$search[enddate]}'";
		
		$morderstat = $db->fetch_first("SELECT COUNT(1) AS DATECOUNT FROM (SELECT DATE_FORMAT(CREATETIME, '%Y-%m') AS DATELINE FROM tbl_order WHERE STATUS > 0 {$search[wheresql]} GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m')) as temp");
		if($morderstat) $morderstat['AVG_ORDERS'] = round($orderstat['STATUS'] / $morderstat['DATECOUNT'], 0);
		
		//订单单价
		$avgorderstat['AVG_TOTALPRICE'] = round($salestat['ALL'] / $orderstat['STATUS'], 2);
		
		//转化率
		$changeorderstat['ALL'] = round($orderstat['STATUS'] * 100 / $viewstat['VIEWS'], 4);
		
		//会员订单量
		$userorderstat['ALL'] = round($orderstat['STATUS'] / $userstat['USERS'], 2);
		
		//购买率
		$orderstat['BUYCOUNT'] = $db->result_first("SELECT COUNT(1) FROM (SELECT USERID FROM tbl_order WHERE STATUS < 5 GROUP BY USERID {$search[wheresql]}) as temp");
		$userstat['BUYPECENT'] = round($orderstat['BUYCOUNT'] * 100 / $userstat['USERS'] , 4);
		
		
		$nowdate = date('Y-m-d');
		$startdate = date('Y-m-d', strtotime("-10 days {$nowdate}"));
		
		$xlist = array();
		for($i = 10; $i >= 0; $i--){
			$xlist[date('m/d', strtotime("-{$i} days {$nowdate}"))] = 0;
		}
		
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND CREATETIME < '{$search[enddate]}'";
		
		$temp_query = $db->query("SELECT DATE_FORMAT(b.CREATETIME, '%m/%d') AS DATELINE, SUM(a.PRICE) AS PRICE, SUM(a.NUM) AS NUM FROM tbl_order_item a, tbl_order b 
		WHERE a.ORDERID = b.ORDERID AND b.STATUS < 5 AND b.CREATETIME > '{$startdate}' {$search[wheresql]} GROUP BY DATE_FORMAT(b.CREATETIME, '%Y-%m-%d')");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$xlist[$row['DATELINE']] = $row;
		}
		
		include_once view('/module/stat/view/allone');
	}
	
}
?>