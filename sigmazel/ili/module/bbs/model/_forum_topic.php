<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\model;

/**
 * 贴子
 * @author sigmazel
 * @since v1.0.2
 */
class _forum_topic{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = '';
		$wheresql = '';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.EDITTIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.EDITTIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.USERNAME, a.TITLE, a.SUMMARY, a.KEYWORDS) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.SUMMARY LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 4) $wheresql .= " AND a.KEYWORDS LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltSForumId']) {
			$querystring .= '&sltSForumId='.$_var['gp_sltSForumId'];
			$wheresql .= " AND a.FORUMID  = '{$_var[gp_sltSForumId]}'";
		}
		
		if($_var['gp_sltIsTop']) {
			$querystring .= '&sltIsTop='.$_var['gp_sltIsTop'];
			if($_var['gp_sltIsTop'] == 1) $wheresql .= " AND a.ISTOP = 1";
			elseif($_var['gp_sltIsTop'] == 2) $wheresql .= " AND a.ISTOP = 0";
		}
		
		if($_var['gp_sltIsAudit']) {
			$querystring .= '&sltIsAudit='.$_var['gp_sltIsAudit'];
			if($_var['gp_sltIsAudit'] == 1) $wheresql .= " AND a.ISAUDIT = 1";
			elseif($_var['gp_sltIsAudit'] == 2) $wheresql .= " AND a.ISAUDIT = 0";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.*, b.NAME AS FORUMNAME FROM tbl_forum_topic a , tbl_forum b WHERE a.FORUMID = b.FORUMID AND a.FORUM_TOPICID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_forum_topic a, tbl_forum b WHERE a.FORUMID = b.FORUMID {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$_forum = new _forum();
		
		!$ordersql && $ordersql = "ORDER BY a.ISTOP DESC, a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		$temp_query = $db->query("SELECT a.*, b.NAME AS FORUMNAME FROM tbl_forum_topic a, tbl_forum b WHERE a.FORUMID = b.FORUMID {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			
			$row['SUMMARY'] = $_forum->format_face($row['SUMMARY']);
			$row['USERNAME'] = $row['USERNAME'] ? $row['USERNAME'] : $GLOBALS['lang']['bbs.topic.view.td.guest'];
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取文件列表
	public function get_files($topic, $filenum){
		$topic_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($topic['FILE'.sprintf('%02d', $i)])) $topic_files[] = $topic['FILE'.sprintf('%02d', $i)];
		}
	
		return $topic_files;
	}
	
	//格式化记录
	public function format($topic, $first = null){
		$_forum_post = new _forum_post();
		
		$topic = format_row_files($topic);
		$topic_files =  $this->get_files($topic, 4);
		
		if($first == null) $first = $_forum_post->get_first($topic['FORUM_TOPICID']);
		
		$topic['CONTENT'] = $first['CONTENT'];
		
		for($i = 0; $i < 4; $i++){
			if(strexists($topic['CONTENT'], '{FILE0'.($i + 1).'}')){
				$topic['CONTENT'] = str_replace('{FILE0'.($i + 1).'}', '<p class="file"><img src="'.$topic_files[$i][3].'" border="0"/></p>', $topic['CONTENT']);
				$topic_files[$i] = '';
			}
		}
		
		$topic['FILES'] = $topic_files;
		
		return $topic;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_forum_topic', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
	
		$db->update('tbl_forum_topic', $data, "FORUM_TOPICID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_forum_topic', "FORUM_TOPICID = '{$id}'");
		$db->delete('tbl_forum_post', "FORUM_TOPICID = '{$id}'");
	}
	
	//刷新点击量
	public function flash_hits($id){
		global $db;
		
		$db->query("UPDATE tbl_forum_topic SET HITS = HITS + 1 WHERE FORUM_TOPICID = '{$id}'");
	}
}
?>