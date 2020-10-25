<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

/**
 * 关键词
 * @author sigmazel
 * @since v1.0.2
 */
class _keyword{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_keyword WHERE KEYWORDID = '{$id}'");
	}
	
	//根据关键词获取记录
	public function get_by_word($word){
		global $db;
	
		return $db->fetch_first("SELECT * FROM tbl_keyword WHERE WORD = '{$word}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM tbl_keyword a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.RANK DESC, a.LETTER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_keyword a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取文件列表
	public function get_files($keyword, $filenum){
		$keyword_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($keyword['FILE'.sprintf('%02d', $i)])) $keyword_files[] = $keyword['FILE'.sprintf('%02d', $i)];
		}
	
		return $keyword_files;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_keyword', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_keyword', $data, "KEYWORDID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_keyword', "KEYWORDID = '{$id}'");
	}
	
	//保存
	public function save($keywords){
		global $db;
		
		$keywords = str_replace('，', ',', $keywords);
		$words = explode(',', $keywords);
		
		foreach ($words as $key => $word){
			if(empty($word)) continue;
			
			$tempword = $db->fetch_first("SELECT * FROM tbl_keyword WHERE WORD = '{$word}'");
			if(!$tempword){
				$db->insert('tbl_keyword', array(
				'WORD' => $word, 
				'LETTER' => pinyin($word)
				));
			}
			
			unset($tempword);
		}
	}
	
}
?>