<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\model;

/**
 * 题目
 * @author sigmazel
 * @since v1.0.2
 */
class _exam_option{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
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
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TITLE, a.USERNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_sltSCategoryID']) {
			$querystring .= '&sltSCategoryID='.$_var['gp_sltSCategoryID'];
			$wheresql .= " AND a.EXAM_CATEGORYID = '{$_var[gp_sltSCategoryID]}'";
		}
		
		if($_var['gp_sltReType']) {
			$querystring .= '&sltReType='.$_var['gp_sltReType'];
			if($_var['gp_sltReType'] == 1) $wheresql .= " AND a.RETYPE = 0";
			elseif($_var['gp_sltReType'] == 2) $wheresql .= " AND a.RETYPE = 1";
			elseif($_var['gp_sltReType'] == 3) $wheresql .= " AND a.RETYPE = 2";
			elseif($_var['gp_sltReType'] == 4) $wheresql .= " AND a.RETYPE = 3";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		$exam_option = $db->fetch_first("SELECT * FROM tbl_exam_option WHERE EXAM_OPTIONID = '{$id}'");
		if($exam_option) $exam_option = $this->format($exam_option);
		
		return $exam_option;
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_exam_option a LEFT JOIN tbl_exam_category b ON a.EXAM_CATEGORYID = b.EXAM_CATEGORYID WHERE 1 {$wheresql}");
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = ''){
		global $db;
		
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.*, b.CNAME FROM tbl_exam_option a LEFT JOIN tbl_exam_category b ON a.EXAM_CATEGORYID = b.EXAM_CATEGORYID WHERE 1 {$wheresql} ORDER BY a.DISPLAYORDER ASC {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = $this->format($row);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取文件
	public function get_files($exam_option, $filenum){
		$exam_optionfiles = array();
		for($i = 1; $i <= $filenum; $i++){
			if(is_array($exam_option['FILE'.sprintf('%02d', $i)])) $exam_optionfiles[] = $exam_option['FILE'.sprintf('%02d', $i)];
		}
	
		return $exam_optionfiles;
	}
	
	//随机抽题
	public function random($exam){
		global $db;
		
		$_exam_category = new _exam_category();
		
		$rows = array();
		$categories = $_exam_category->get_list(0, 0, "AND a.EXAMID = '{$exam[EXAMID]}'");
		
		if(count($categories) > 0){
			foreach($categories as $key => $category){
				if($category['NUM'] + 0 > 0){
					if($exam['RAND'] == 1) $tsql = "SELECT * FROM tbl_exam_option WHERE EXAMID = '{$exam[EXAMID]}' AND EXAM_CATEGORYID = '{$category[EXAM_CATEGORYID]}' ORDER BY RAND()"; 
					else $tsql = "SELECT * FROM tbl_exam_option WHERE EXAMID = '{$exam[EXAMID]}' AND EXAM_CATEGORYID = '{$category[EXAM_CATEGORYID]}' ORDER BY DISPLAYORDER ASC";
					
					$temp_query = $db->query("{$tsql} LIMIT 0, {$category[NUM]}");
					while(($row = $db->fetch_array($temp_query)) !== false){
						$row = $this->format($row);
						
						$rows[] = $row;
					}
				}
				
				unset($tsql);
				unset($temp_query);
			}
		}
		
		if(count($rows) == 0){
			if($exam['RAND'] == 1) $tsql = "SELECT * FROM tbl_exam_option WHERE EXAMID = '{$exam[EXAMID]}' ORDER BY RAND()"; 
			else $tsql = "SELECT * FROM tbl_exam_option WHERE EXAMID = '{$exam[EXAMID]}' ORDER BY DISPLAYORDER ASC";
			
			$temp_query = $db->query("{$tsql} LIMIT 0, 20");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$row = $this->format($row);
				
				$rows[] = $row;
			}
		}
		
		return $rows;
	}
	
	//格式化记录
	public function format($exam_option){
		if($exam_option['RETYPE'] == 1){
			$exam_option = format_row_file($exam_option, 'REMARK');
			$exam_option['REFILE'] = $exam_option['REMARK'];
		}elseif($exam_option['RETYPE'] == 2) $exam_option['REVIDEO'] = $exam_option['REMARK'];
		elseif($exam_option['RETYPE'] == 2) $exam_option['REAUDIO'] = $exam_option['REMARK'];
		else $exam_option['RETEXT'] = $exam_option['REMARK'];
		
		$exam_option['ITEMS'] = array();
		
		$items[0] = explode('|', $exam_option['FILE01']);
		$items[1] = explode('|', $exam_option['FILE02']);
		$items[2] = explode('|', $exam_option['FILE03']);
		$items[3] = explode('|', $exam_option['FILE04']);
		$items[4] = explode('|', $exam_option['FILE05']);
		$items[5] = explode('|', $exam_option['FILE06']);
		
		for($i = 0; $i < 6; $i++){
			if(count($items[$i]) < 4) continue;
			
			$item = array();
			$item['ITEMID'] = $i + 1;
			$item['ANSWER'] = $items[$i][0];
			$item['DISPLAYORDER'] = $items[$i][1];
			$item['TITLE'] = $items[$i][2];
			$item['_FILE01'] = $items[$i][3];
			$item['FILE01'] = $items[$i][3] ? format_file_path($items[$i][3]) : $items[$i][3];
			
			$exam_option['ITEMS'][] = $item;
		}
		
		usort($exam_option['ITEMS'], "_exam_option_displayorder");
		
		return $exam_option;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_exam_option', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_exam_option', $data, "EXAM_OPTIONID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_exam_option', "EXAM_OPTIONID = '{$id}'");
		$db->delete('tbl_exam_record', "EXAM_OPTIONID = '{$id}'");
	}
	
}

function _exam_option_displayorder($a, $b){
    if ($a['DISPLAYORDER'] == $b['DISPLAYORDER']) return ($a['ITEMID'] > $b['ITEMID']) ? 1 : 0;
    
    return ($a['DISPLAYORDER'] < $b['DISPLAYORDER']) ? -1 : 1;
}
?>