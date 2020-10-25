<?php
//版权所有(C) 2014 www.ilinei.com

namespace stat\control;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/stat/lang.php';

//浏览量
class viewlog{
	//默认
	public function index(){
		global $_var, $db;
		
		
		$date_type = $_var['gp_type'] + 0;
		
		if($date_type == 0){
            $nowdate = date('Y-m-d');
			$nowmonth = date('Y-m-d', strtotime("-30 days {$nowdate}"));
            $nextmonth = date('Y-m').'-01';

			$temp_query = $db->query("SELECT DATE_FORMAT(DATELINE, '%m/%d') AS DATELINE, SUM(VIEWS) AS VIEWS FROM tbl_view_hour 
			WHERE DATELINE >= '{$nowmonth}' AND DATELINE < '{$nextmonth}' GROUP BY DATE_FORMAT(DATELINE, '%Y-%m-%d')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['VIEWS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(DATELINE, '%H') AS DATELINE, SUM(VIEWS) AS VIEWS FROM tbl_view_hour 
			WHERE DATELINE >= '{$nowmonth}' AND DATELINE < '{$nextmonth}' GROUP BY DATE_FORMAT(DATELINE, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['VIEWS']; 
			}
			
		}elseif($date_type == 1){
			$nowmonth = date('Y').'-01-01';
			$nextmonth = date('Y'.'-01-01', strtotime("+1 years {$nowdate}"));
		
			$temp_query = $db->query("SELECT DATE_FORMAT(DATELINE, '%m') AS DATELINE, SUM(VIEWS) AS VIEWS FROM tbl_view_hour 
			WHERE DATELINE >= '{$nowmonth}' AND DATELINE < '{$nextmonth}' GROUP BY DATE_FORMAT(DATELINE, '%Y-%m')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xlist[$row['DATELINE']] = $row['VIEWS']; 
			}
			
			$temp_query = $db->query("SELECT DATE_FORMAT(DATELINE, '%H') AS DATELINE, SUM(VIEWS) AS VIEWS FROM tbl_view_hour 
			WHERE DATELINE >= '{$nowmonth}' AND DATELINE < '{$nextmonth}' GROUP BY DATE_FORMAT(DATELINE, '%H')");
			
			while(($row = $db->fetch_array($temp_query)) !== false){
				$xhour_list[$row['DATELINE']] = $row['VIEWS']; 
			}
		}
		
		include_once view('/module/stat/view/viewlog');
	}
	
}
?>