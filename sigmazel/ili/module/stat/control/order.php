<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//订单量
class order{
	//默认
	public function index(){
		global $_var, $db;
		
		$date_type = $_var['gp_type'] + 0;
		
		$stat = array(
			'ORDER' => array('ALIPAY' => 0, 'WEIXIN' => 0, 'ACCOUNT' => 0), 
			'CARD' => array('CREDIT' => 0, 'ONLINE' => 0), 
			'CREDIT' => array('CREDIT' => 0, 'SCORE' => 0), 
			'USER' => array('CREDIT' => 0, 'SCORE' => 0)
		);
		
		$stat['ORDER']['ALIPAY'] = $db->result_first("SELECT SUM(TOTALPRICE) FROM tbl_order WHERE PAY = 1 AND INSTR(PAYTYPE, 'online') > 0 AND PAYMETHOD <> '微信支付'");
		$stat['ORDER']['WEIXIN'] = $db->result_first("SELECT SUM(TOTALPRICE) FROM tbl_order WHERE PAY = 1 AND PAYMETHOD = '微信支付'");
		$stat['ORDER']['ACCOUNT'] = $db->result_first("SELECT SUM(TOTALPRICE) FROM tbl_order WHERE PAY = 1 AND INSTR(PAYTYPE, 'online') = 0");
		$stat['ORDER']['ALL'] = $db->result_first("SELECT SUM(TOTALPRICE) FROM tbl_order WHERE PAY = 1");
		
		$stat['CARD']['CREDIT'] = $db->result_first("SELECT SUM(CREDIT) FROM tbl_card WHERE SERIAL NOT LIKE 'CO%' AND STATUS = 1");
		$stat['CARD']['ONLINE'] = $db->result_first("SELECT SUM(CREDIT) FROM tbl_card WHERE SERIAL LIKE 'CO%' AND STATUS = 1");
		
		$stat['CREDIT']['CREDIT'] = $db->result_first("SELECT SUM(CREDIT) FROM tbl_credit WHERE CREDIT > 0");
		$stat['CREDIT']['SCORE'] = $db->result_first("SELECT SUM(SCORE) FROM tbl_credit");
		
		$stat['USER']['CREDIT'] = $db->result_first("SELECT SUM(CREDIT) FROM tbl_user");
		$stat['USER']['SCORE'] = $db->result_first("SELECT SUM(SCORE) FROM tbl_user");
		
		$stat['IN'] = $stat['ORDER']['ALIPAY'] + $stat['ORDER']['WEIXIN'] + $stat['CARD']['CREDIT'] + $stat['CARD']['ONLINE'] + 0;
		$stat['OUT'] = $stat['ORDER']['ALL'] + $stat['USER']['CREDIT'] + 0;
		
		if($date_type == 0){
            $nowdate = date('Y-m-d');
			$nowmonth = date('Y-m-d', strtotime("-30 days {$nowdate}"));
			$nextmonth = date('Y-m').'-01';
		
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%m/%d') AS DATELINE, COUNT(1) AS USERS FROM tbl_order 
			WHERE STATUS > 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m-%d')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['USERS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%H') AS DATELINE, COUNT(1) AS ORDERS FROM tbl_order 
			WHERE STATUS > 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['ORDERS']; 
			}
		}elseif($date_type == 1){
            $nowdate = date('Y-m-d');
			$nowmonth = date('Y').'-01-01';
			$nextmonth = date('Y'.'-01-01', strtotime("+1 years {$nowdate}"));
		
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%m') AS DATELINE, COUNT(1) AS ORDERS FROM tbl_order 
			WHERE STATUS > 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['ORDERS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%H') AS DATELINE, COUNT(1) AS ORDERS FROM tbl_order 
			WHERE STATUS > 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['ORDERS']; 
			}
		}
		
		include_once view('/module/stat/view/order');
	}
	
	
}
?>