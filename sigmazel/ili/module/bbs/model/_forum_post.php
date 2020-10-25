<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\model;

/**
 * 回复
 * @author sigmazel
 * @since v1.0.2
 */
class _forum_post{
	//获取第一条记录
	public function get_first($topicid){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_forum_post a WHERE a.FORUM_TOPICID = '{$topicid}' AND a.FIRST = 1");
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_forum_post a WHERE a.FORUM_POSTID = '{$id}' AND a.FIRST = 0");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_forum_post a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$_forum = new _forum();
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		$temp_query = $db->query("SELECT a.*, u.MOBILE, u.PHOTO FROM tbl_forum_post a LEFT JOIN tbl_user u ON a.USERID = u.USERID WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CONTENT'] =  $_forum->format_face($row['CONTENT']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_forum_post', $data);
		$insertid = $db->insert_id();
		
		$post_count = $this->get_count("AND a.FORUM_TOPICID = '{$data[FORUM_TOPICID]}' AND a.FIRST = 0");
		
		$db->update('tbl_forum_topic', array(
		'POSTCOUNT' => $post_count, 
		'LASTPOST' => substr($data['EDITTIME'], 5). ' | '.$data['USERNAME'], 
		'LASTTIME' => date('Y-m-d h:i:s')
		), "FORUM_TOPICID = '{$data[FORUM_TOPICID]}'");
		
		return $insertid;
	}
	
	//修改
	public function update($id, $data){
		global $db;
	
		$db->update('tbl_forum_post', $data, "FORUM_POSTID = '{$id}'");
	}
	
	//批量修改
	public function update_batch($where, $data){
		global $db;
	
		$db->update('tbl_forum_post', $data, $where);
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_forum_post', "FORUM_POSTID = '{$id}'");
	}
	
}
?>