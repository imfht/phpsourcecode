<?php
//版权所有(C) 2014 www.ilinei.com

namespace misc\control;

use admin\model\_viewlog;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//任务
class cron{
	//浏览日志
	public function viewlog(){
		global $db;
		
		$db->connect();
		
		$_viewlog = new _viewlog();
		
		$dirs = scandir(ROOTPATH.'/_cache/viewlog');
		$todayfile = date('Ymd00') + 0;
		
		foreach ($dirs as $file){
			if(strlen($file) == 10 && $file + 0 < $todayfile){
				$fp = fopen(ROOTPATH.'/_cache/viewlog/'.$file, 'r');
				$temp_logs = array();
				while (!feof($fp)) {
					$temp_logs[] = fgets($fp);
				}
				
				fclose($fp);
				
				foreach($temp_logs as $key => $log){
					$temp_log_arr = explode('|', $log);
					if(count($temp_log_arr) == 5){
						if(check_robot($temp_log_arr[4])) continue;
						
						$temp_date = substr($temp_log_arr[0], 0, 10);
							
						$temp_count = $_viewlog->get_count("AND AUTH = '{$temp_log_arr[2]}' AND DATE_FORMAT(DATELINE, '%Y-%m-%d') = '{$temp_date}'");
						if($temp_count > 0) continue;
							
						$_viewlog->insert(array(
						'DATELINE' => trim($temp_log_arr[0]),
						'IP' => $temp_log_arr[1],
						'AUTH' => $temp_log_arr[2],
						'REQUEST_URI' => $temp_log_arr[3],
						'HTTP_USER_AGENT' => $temp_log_arr[4]
						));
					}
		
					unset($temp_log_arr);
					unset($temp_date);
					unset($temp_count);
				}
					
				unlink(ROOTPATH.'/_cache/viewlog/'.$file);
					
				$message['nexting'] = true;
				exit_json($message);
			}
		
			unset($fp);
			unset($temp_logs);
		}
		
		$_viewlog->analyse();
	}
}
?>