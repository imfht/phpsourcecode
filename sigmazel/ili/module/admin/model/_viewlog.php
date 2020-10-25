<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 浏览日志
 * @author sigmazel
 * @since v1.0.2
 */
class _viewlog{
	//获取日志数量 
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_view_log WHERE 1 {$wheresql}") + 0;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_view_log', $data);
		
		return $db->insert_id();
	}
	
	//删除
	public function analyse(){
		global $db;
		
		$temp_count = $db->result_first("SELECT COUNT(1) FROM tbl_view_log") + 0;
		if($temp_count > 0){
			$temp_query = $db->query("SELECT * FROM tbl_view_log");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$temp_hour = date('Y-m-d H:00:00', strtotime($row['DATELINE']));
				
				$temp_hour_count = $db->result_first("SELECT COUNT(1) FROM tbl_view_hour WHERE DATELINE = '{$temp_hour}'") + 0;
				if($temp_hour_count == 0) $db->insert('tbl_view_hour', array('DATELINE' => $temp_hour, 'VIEWS' => 1));
				else $db->query("UPDATE tbl_view_hour SET VIEWS = VIEWS + 1 WHERE DATELINE = '{$temp_hour}'");
					
				unset($temp_hour);
				unset($temp_hour_count);
			}
			
			$db->delete('tbl_view_log');
		}
	}
	
}
?>