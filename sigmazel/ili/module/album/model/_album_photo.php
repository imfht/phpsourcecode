<?php
//版权所有(C) 2014 www.ilinei.com

namespace album\model;

/**
 * 相册图片
 * @author sigmazel
 * @since v1.0.2
 */
class _album_photo{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_album_photo WHERE ALBUM_PHOTOID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_album_photo a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = 'ORDER BY a.DISPLAYORDER ASC';
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_album_photo a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$row['INFO'] = unserialize($row['INFO']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取所有记录
	public function get_all($id, $ordersql = ''){
		global $db;
		
		$wheresql = $id != -1 ? " AND a.ALBUMID = '{$id}'" : '';
		!$ordersql && $ordersql = 'ORDER BY a.DISPLAYORDER ASC';
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_album_photo a WHERE 1 {$wheresql} {$ordersql} ");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$row['INFO'] = unserialize($row['INFO']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取统计
	public function get_stat($albumid){
		global $db;
		
		return $db->fetch_first("SELECT SUM(SIZE) AS SIZE, COUNT(1) AS PHOTOS FROM tbl_album_photo a GROUP BY a.ALBUMID HAVING a.ALBUMID = '{$albumid}'");
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_album_photo', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_album_photo', $data, "ALBUM_PHOTOID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
		
		$db->update('tbl_album_photo', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_album_photo', "ALBUM_PHOTOID = '{$id}'");
	}
	
	//批量删除
	public function delete_batch($where){
		global $db;
		
		$db->delete('tbl_album_photo', $where);
	}
	
}
?>