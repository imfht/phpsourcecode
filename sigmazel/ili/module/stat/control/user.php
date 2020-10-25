<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//注册量
class user{
	//默认
	public function index(){
		global $_var, $db;
		
		$date_type = $_var['gp_type'] + 0;
		
		if($date_type == 0){
            $nowdate = date('Y-m-d');
			$nowmonth = date('Y-m-d', strtotime("-30 days {$nowdate}"));
            $nextmonth = date('Y-m').'-01';

			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%m/%d') AS DATELINE, COUNT(1) AS USERS FROM tbl_user 
			WHERE ISMANAGER = 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m-%d')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['USERS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%H') AS DATELINE, COUNT(1) AS USERS FROM tbl_user 
			WHERE ISMANAGER = 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['USERS']; 
			}
			
		}elseif($date_type == 1){
			$nowmonth = date('Y').'-01-01';
			$nextmonth = date('Y'.'-01-01', strtotime("+1 years {$nowmonth}"));
		
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%m') AS DATELINE, COUNT(1) AS USERS FROM tbl_user 
			WHERE ISMANAGER = 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%Y-%m')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['USERS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(CREATETIME, '%H') AS DATELINE, COUNT(1) AS USERS FROM tbl_user 
			WHERE ISMANAGER = 0 AND CREATETIME >= '{$nowmonth}' AND CREATETIME < '{$nextmonth}' GROUP BY DATE_FORMAT(CREATETIME, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['USERS']; 
			}
		}
		
		include_once view('/module/stat/view/user');
	}
	
}
?>