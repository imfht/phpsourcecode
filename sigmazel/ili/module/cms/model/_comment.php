<?php
//版权所有(C) 2014 www.ilinei.com

namespace cms\model;

/**
 * 评论
 * @author sigmazel
 * @since v1.0.2
 */
class _comment{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = '';
		$wheresql = ' ';
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.CREATETIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.CREATETIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.USERNAME, a.TITLE, a.CONTENT) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.CONTENT LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltIsAudit']) {
			$querystring .= '&sltIsAudit='.$_var['gp_sltIsAudit'];
			if($_var['gp_sltIsAudit'] == 1) $wheresql .= " AND a.ISAUDIT  = 1";
			elseif($_var['gp_sltIsAudit'] == 2) $wheresql .= " AND a.ISAUDIT  = 0";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($commentid){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_comment a WHERE a.COMMENTID = '{$commentid}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_comment a LEFT JOIN tbl_user b ON a.USERID = b.USERID WHERE 1 {$wheresql}") + 0;
	}
	
	//获取数量+文章
	public function get_count_of_article($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_comment a, tbl_article b WHERE a.ABOUTID = b.ARTICLEID AND a.ABOUTTYPE = 'article' {$wheresql}") + 0;
	}
	
	//获取数量+商品
	public function get_count_of_product($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_comment a, tbl_product b WHERE a.ABOUTID = b.PRODUCTID AND a.ABOUTTYPE = 'product' {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = 'ORDER BY a.CREATETIME DESC';
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.REALNAME, b.PHOTO FROM tbl_comment a LEFT JOIN tbl_user b ON a.USERID = b.USERID WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CONTENT'] = nl2br($row['CONTENT']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取列表+文章
	public function get_list_of_article($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = 'ORDER BY a.CREATETIME DESC';
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.TITLE AS ARTICLE_TITLE FROM tbl_comment a, tbl_article b WHERE a.ABOUTID = b.ARTICLEID AND a.ABOUTTYPE = 'article' {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CONTENT'] = nl2br($row['CONTENT']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取列表+商品
	public function get_list_of_product($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = 'ORDER BY a.CREATETIME ASC';
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.TITLE AS PRODUCT_TITLE FROM tbl_comment a, tbl_product b WHERE a.ABOUTID = b.PRODUCTID AND a.ABOUTTYPE = 'product' {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CONTENT'] = nl2br($row['CONTENT']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//分组计数
	public function group($array, $type = 'article'){
		global $db;
		
		if(count($array) == 0) return $array;
		
		$aboutids = array();
		foreach($array as $key => $arr){
			if($type == 'article') $aboutids[] = $arr['ARTICLEID'];
			elseif($type == 'product') $aboutids[] = $arr['PRODUCTID'];
		}
		
		$temp_query = $db->query("SELECT ABOUTID, COUNT(1) AS COMMENTS FROM tbl_comment WHERE ABOUTID IN(".eimplode($aboutids).") AND ABOUTTYPE = '{$type}' GROUP BY ABOUTID");
		while(($row = $db->fetch_array($temp_query)) !== false){
			foreach($array as $key => $arr){
				if($type == 'article' && $row['ABOUTID'] == $arr['ARTICLEID']){
					$array[$key]['COMMENTS'] = $row['COMMENTS'];
					break;
				}
				
				if($type == 'product' && $row['ABOUTID'] == $arr['PRODUCTID']){
					$array[$key]['COMMENTS'] = $row['COMMENTS'];
					break;
				}
			}
		}
		
		return $array;
	}
	
	//数据统计
	public function get_stat($wheresql = ''){
		global $db;
		
		return $db->fetch_first("SELECT COUNT(1) AS CNT, SUM(LEVEL) AS LEVEL, SUM(SCORE) AS SCORE, SUM(ATTENT) AS ATTENT, SUM(UP) AS UP, SUM(DOWN) AS DOWN FROM tbl_comment a WHERE 1 {$wheresql}");
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_comment', $data);
	}
	
	//修改
	public function update($id, $data){
		global $db;
	
		$db->update('tbl_comment', $data, "COMMENTID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_comment', "COMMENTID = {$id}");
		$db->delete('tbl_comment', "PARENTID = {$id}");
	}
	
	
}
?>