<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

/**
 * 文章内容
 * @author sigmazel
 * @since v1.0.2
 */
class _article_content{
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_article_content WHERE ARTICLEID = '{$id}'");
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_article_content', $data);
	}
	
	//修改
	public function update($id, $data){
		global $db;
	
		$db->update('tbl_article_content', $data, "ARTICLEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_article_content', "ARTICLEID = '{$id}'");
	}
}
?>