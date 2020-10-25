<?php
//版权所有(C) 2014 www.ilinei.com

namespace ad\model;

/**
 * 广告位
 * @author sigmazel
 * @since v1.0.2
 */
class _ad{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获得广告位
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_ad WHERE ADID = '{$id}' LIMIT 0, 1");
	}
	
	//根据标识号获得广告位
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_ad WHERE IDENTITY = '{$identity}' LIMIT 0, 1");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_ad a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.IDENTITY ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_ad a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获得广告位列表
	public function get_all(){
		global $db;
		
		$ads = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_ad a ORDER BY a.IDENTITY ASC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$ads[] = $row;
		}
		
		return $ads;
	}
	
	//获得文件列表
	public function get_files($ad, $filenum){
		$ad_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($ad['FILE'.sprintf('%02d', $i)])) $ad_files[] = $ad['FILE'.sprintf('%02d', $i)];
		}
	
		return $ad_files;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_ad', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_ad', $data, "ADID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_ad', "ADID = '{$id}'");
		$db->delete('tbl_ad_log', "ADID = '{$id}'");
	}

    //显示广告记录
    public function block_display($json){
        $params = json_decode($json, 1);

        if(empty($params['identity'])) return null;
        else{
            $identity = $params['identity'];

            $_ad_log = new _ad_log();
            return $_ad_log->display($identity, 1);
        }
    }
    
}
?>