<?php
//版权所有(C) 2014 www.ilinei.com

namespace album\model;

/**
 * 相册
 * @author sigmazel
 * @since v1.0.2
 */
class _album{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_album WHERE ALBUMID = '{$id}'");
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_album WHERE IDENTITY = '{$identity}'");
	}
	
	//根据标题获取记录
	public function get_by_title($title){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_album WHERE TITLE = '{$title}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_album a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.ALBUMID DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_album a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$row['SIZE'] = format_bytes($row['SIZE']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_album', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_album', $data, "ALBUMID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_album', "ALBUMID = '{$id}'");
		$db->delete('tbl_album_photo', "ALBUMID = '{$id}'");
	}
	
	//模块扩展
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 2){
			$module_data['identity'] = $tmparr[0];
			$module_data['albumid'] = $tmparr[1];
		}else{
			$module_data['identity'] = 'album';
		}
		
		$album_list = $this->get_list(0, 0);
		foreach($album_list as $key => $album){
			$album = $this->module_format($album);
			$album_list[$key]['TIPS'] = $album['TIPS'];
		}
		
		include_once view('/module/album/view/module');
	}
	
	//模块格式化
	public function module_format($album){
		$album['TIPS'] = "{$album[TITLE]} {$album[PHOTOS]}".$GLOBALS['lang']['album.module.view.tips.0']."，{$album[SIZE]}".$GLOBALS['lang']['album.module.view.tips.1'];
		
		return $album;
	}
	
}
?>