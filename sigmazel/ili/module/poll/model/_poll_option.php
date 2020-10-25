<?php
//版权所有(C) 2014 www.ilinei.com

namespace poll\model;

/**
 * 投票项
 * @author sigmazel
 * @since v1.0.2
 */
class _poll_option{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_poll_option WHERE POLL_OPTIONID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_poll_option a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.POLLID ASC, a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_poll_option a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			if(!$row['TITLE']) continue;
			
			$row = format_row_files($row);
			
			$row['_SUMMARY'] = $row['SUMMARY'];
			$row['SUMMARY'] = preg_replace('/\{VOTES([\w\:]*)\}/', '', $row['SUMMARY']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取结果
	public function get_result($options, $virtual = false){
		global $db;
		
		$colors = array('#E75105', '#E38D06', '#4C9ABE', '#5EC069', '#C1568E', '#7A53C0', '#D4B817', '#5DB9A0', '#B27757');
		
		$vote_all = 0;
		
		foreach($options as $key => $option){
			$option['COLOR'] = $colors[$key] ? $colors[$key] : '#E75105';
			$option['VOTECOUNT'] = $db->result_first("SELECT COUNT(1) FROM tbl_poll_vote WHERE POLLID = '{$option[POLLID]}' AND INSTR(VAL, '|{$option[POLL_OPTIONID]}:') > 0") + 0;
			$virtual && $option['VOTECOUNT'] += $option['VOTES'];
			$vote_all += $option['VOTECOUNT'];
			$options[$key] = $option;
		}
		
		foreach($options as $key => $option){
			$options[$key]['PECENT'] = round(($option['VOTECOUNT'] * 100.00) / $vote_all, 2).'%';
		}
		
		return $options;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_poll_option', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_poll_option', $data, "POLL_OPTIONID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_poll_option', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_poll_option', "POLL_OPTIONID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_poll_option', $where);
	}
	
}
?>