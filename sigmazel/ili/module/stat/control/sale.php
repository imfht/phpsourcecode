<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//销售量排行
class sale{
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
		
		//销售总额数
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND b.CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND b.CREATETIME < '{$search[enddate]}'";
		
		$rank_list = array();
		
		$temp_query = $db->query("SELECT p.TITLE, p.FILE01, t.PRICE, p.OURPRICE, t.NUM FROM tbl_product p 
		INNER JOIN (SELECT PRODUCTID, SUM(NUM) AS NUM, PRICE FROM tbl_order_item a, tbl_order b WHERE a.ORDERID = b.ORDERID {$search[wheresql]} GROUP BY PRODUCTID ORDER BY NUM DESC LIMIT 0, 20) AS t ON p.PRODUCTID = t.PRODUCTID");
		while(($row = $db->fetch_array($temp_query)) !== false){
			if($row['FILE01']){
				$row['FILE01'] = explode('|', $row['FILE01']);
				$row['FILE01'][0] = format_file_path($row['FILE01'][0], $row['FILE01'][2] + 0);
			}
			
			$rank_list[] = $row; 
		}
		
		include_once view('/module/stat/view/sale');
	}
	
}
?>