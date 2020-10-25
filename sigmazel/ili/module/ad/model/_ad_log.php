<?php
//版权所有(C) 2014 www.ilinei.com

namespace ad\model;

/**
 * 广告记录
 * @author sigmazel
 * @since v1.0.2
 */
class _ad_log{
	/**
	 * 搜索
	 */
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		$ordersql = 'ORDER BY a.EDITTIME DESC';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.BEGINDATE >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.ENDDATE <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TITLE, a.LINK, a.REMARK, b.TITLE) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 1) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 2) $wheresql .= " AND b.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 3) $wheresql .= " AND a.LINK LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif($_var['gp_sltType'] == 4) $wheresql .= " AND a.REMARK LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltCategoryId']) {
			$querystring .= '&sltCategoryId='.$_var['gp_sltCategoryId'];
			$wheresql .= " AND a.CATEGORYID  = '{$_var[gp_sltCategoryId]}'";
		}
		
		if($_var['gp_sltAdId']) {
			$querystring .= '&sltAdId='.$_var['gp_sltAdId'];
			$wheresql .= " AND a.ADID  = '{$_var[gp_sltAdId]}'";
		}
		
		if($_var['gp_sltSort']) {
			$querystring .= '&sltSort='.$_var['gp_sltSort'];
			
			if($_var['gp_sltSort'] == 1) $ordersql = 'ORDER BY a.ADID ASC, a.SORTNO ASC, a.AD_LOGID ASC';
			elseif($_var['gp_sltSort'] == 2) $ordersql = " ORDER BY a.EDITTIME ASC";
			elseif($_var['gp_sltSort'] == 3) $ordersql = " ORDER BY a.EDITTIME DESC";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql, 'ordersql' => $ordersql);
	}

	/**
	 * 根据ID获取记录
	 * @param integer $id
	 * @return  $row 单条
	 */
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_ad_log WHERE AD_LOGID = '{$id}'");
	}
	
	/**
	 * 获取数量
	 * @param string $wheresql 查询条件
	 * @return int 数量
	 */
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_ad_log a , tbl_ad b WHERE a.ADID = b.ADID {$wheresql}") + 0;
		
	}
	
	/**
	 * 获取列表
	 * @param integer $start 起始数
	 * @param integer $perpage 数量
	 * @param string $wheresql 查询条件
	 * @param string $ordersql 排序条件
	 * @return $rows 列表
	 */
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.*, b.TITLE AS ADNAME FROM tbl_ad_log a, tbl_ad b WHERE a.ADID = b.ADID {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$row['BEGINDATE'] = $row['BEGINDATE'] > 0 ? date('Y-m-d', strtotime($row['BEGINDATE'])) : '';
			$row['ENDDATE'] = $row['ENDDATE'] > 0 ? date('Y-m-d', strtotime($row['ENDDATE'])) : '';
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	/**
	 * 获得文件列表
	 * @param array $adlog
	 * @param integer $filenum 文件数量
	 * @return $rows
	 */
	public function get_files($adlog, $filenum){
		$adlog_files = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($adlog['FILE'.sprintf('%02d', $i)])) $adlog_files[] = $adlog['FILE'.sprintf('%02d', $i)];
		}
	
		return $adlog_files;
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return integer
	 */
	public function insert($data){
		global $db;
		
		$db->insert('tbl_ad_log', $data);
		
		return $db->insert_id();
	}
	
	/**
	 * 修改
	 * @param integer $id
	 * @param array $data
	 */
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_ad_log', $data, "AD_LOGID = '{$id}'");
	}
	
	/**
	 * 删除
	 * @param integer $id
	 */
	public function delete($id){
		global $db;
		
		$db->delete('tbl_ad_log', "AD_LOGID = '{$id}'");
	}
	
	/**
	 * 显示记录
	 * @param string $identity 广告位标识号
	 * @param integer $fetch_array 是否返回数组 
	 * @return string|$rows
	 */
	public function display($identity, $fetch_array = 0){
		global $db, $_var;
		
		$_ad = new _ad();
		
		$identitis = explode(',', $identity);
		$ad = $_ad->get_by_identity($identitis[0]);
		
		if($ad == null) return $fetch_array ? array() : '';
		
		$ad = format_row_files($ad);
		
		$whstring = '';
		if($ad['WIDTH']) $whstring .= " width='{$ad[WIDTH]}'";
		if($ad['HEIGHT']) $whstring .= " height='{$ad[HEIGHT]}'";
		
		$ad_logs = array();
		
		$nowdate = date('Y-m-d');
		
		if(count($identitis) == 2) $wheresql = " AND a.CATEGORYID = '".($identitis[1] > 0 ? $identitis[1] : $_var['gp_'.$identitis[1]])."'";
		else $wheresql = "";
		
		if($ad['TYPE'] == 0 || $ad['TYPE'] == 1){
			$temp_query = $db->query("SELECT * FROM tbl_ad_log a WHERE a.ADID = '{$ad[ADID]}' {$wheresql} AND (a.BEGINDATE = '0000-00-00' OR a.BEGINDATE <= '$nowdate') AND (a.ENDDATE = '0000-00-00' OR a.ENDDATE >= '$nowdate') ORDER BY a.SORTNO ASC");
			while(($row = $db->fetch_array($temp_query)) !== false){
				if($ad['WIDTH']) $row['WIDTH'] = $ad['WIDTH'];
				if($ad['HEIGHT']) $row['HEIGHT'] = $ad['HEIGHT'];
				
				$row['REMARK'] = nl2br($row['REMARK']);
				$row = format_row_files($row);
				
				$ad_logs[] = $row;
			}
		}elseif($ad['TYPE'] == 2){
			$temp_query = $db->query("SELECT * FROM tbl_ad_log a WHERE a.ADID = '{$ad[ADID]}' {$wheresql} AND (a.BEGINDATE = '0000-00-00' OR a.BEGINDATE <= '$nowdate') AND (a.ENDDATE = '0000-00-00' OR a.ENDDATE >= '$nowdate') ORDER BY a.SORTNO ASC LIMIT 0, 1");
			while(($row = $db->fetch_array($temp_query)) !== false){
				if($ad['WIDTH']) $row['WIDTH'] = $ad['WIDTH'];
				if($ad['HEIGHT']) $row['HEIGHT'] = $ad['HEIGHT'];
				
				$row['REMARK'] = nl2br($row['REMARK']);
				$row = format_row_files($row);
				
				$ad_logs[] = $row;
			}
		}elseif($ad['TYPE'] == 3){
			$temp_query = $db->query("SELECT * FROM tbl_ad_log a WHERE a.ADID = '{$ad[ADID]}' {$wheresql} AND (a.BEGINDATE = '0000-00-00' OR a.BEGINDATE <= '$nowdate') AND (a.ENDDATE = '0000-00-00' OR a.ENDDATE >= '$nowdate') ORDER BY rand()");
			while(($row = $db->fetch_array($temp_query)) !== false){
				if($ad['WIDTH']) $row['WIDTH'] = $ad['WIDTH'];
				if($ad['HEIGHT']) $row['HEIGHT'] = $ad['HEIGHT'];
				
				$row['REMARK'] = nl2br($row['REMARK']);
				$row = format_row_files($row);
				
				$ad_logs[] = $row;
			}
		}elseif($ad['TYPE'] == 4){
			$temp_query = $db->query("SELECT * FROM tbl_ad_log a WHERE a.ADID = '{$ad[ADID]}' {$wheresql} AND (a.BEGINDATE = '0000-00-00' OR a.BEGINDATE <= '$nowdate') AND (a.ENDDATE = '0000-00-00' OR a.ENDDATE >= '$nowdate') ORDER BY rand() LIMIT 0, 1");
			while(($row = $db->fetch_array($temp_query)) !== false){
				if($ad['WIDTH']) $row['WIDTH'] = $ad['WIDTH'];
				if($ad['HEIGHT']) $row['HEIGHT'] = $ad['HEIGHT'];
				
				$row['REMARK'] = nl2br($row['REMARK']);
				$row = format_row_files($row);
				
				$ad_logs[] = $row;
			}
		}
		
		if($fetch_array == 1) return $ad_logs;
		
		if(count($ad_logs) > 0){
			$temp_html = '';
			foreach ($ad_logs as $key => $adlog){
				$temp_html .= "<a".($adlog['LINK'] && $adlog['LINK'] != '#' ? " href='{$adlog[LINK]}'" : '')." title='{$adlog[TITLE]}'";
				$temp_html .= " target='_blank'";
				$temp_html .= "><img src='{$adlog[FILE01][3]}' border='0' {$whstring} /></a>";
			}
			
			return $temp_html;
		}
		
		return "<a><img src='{$ad[FILE01][3]}' border='0' {$whstring} /></a>";
	}
	
}
?>