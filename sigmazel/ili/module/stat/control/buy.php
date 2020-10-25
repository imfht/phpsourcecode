<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//购买排行
class buy{
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
		
		
		$search['wheresql'] = '';
		if($search['begindate']) $search['wheresql'] .= " AND b.CREATETIME >= '{$search[begindate]}'";
		if($search['enddate']) $search['wheresql'] .= " AND b.CREATETIME < '{$search[enddate]}'";
		
		$rank_list = array();
		
		$temp_query = $db->query("SELECT u.*, t.TOTALPRICE FROM tbl_user u 
		INNER JOIN (SELECT USERID, SUM(TOTALPRICE) AS TOTALPRICE FROM tbl_order a WHERE 1 {$search[wheresql]} GROUP BY USERID ORDER BY TOTALPRICE DESC LIMIT 0, 20) AS t ON u.USERID = t.USERID");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rank_list[] = $row; 
		}
		
		include_once view('/module/stat/view/buy');
	}
	
}
?>