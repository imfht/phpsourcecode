<?php
//版权所有(C) 2014 www.ilinei.com

namespace exam\model;

/**
 * 答题
 * @author sigmazel
 * @since v1.0.2
 */
class _exam{
	//获取状态值
	public function get_status(){
		return array(
		0 => '未启用', 
		1 => '已启用'
		);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_exam WHERE EXAMID = '{$id}'");
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_exam WHERE IDENTITY = '{$identity}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_exam a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_exam a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			
			$row['BEGINTIME'] = $row['BEGINTIME'] > 0 ? date('Y-m-d H:i', strtotime($row['BEGINTIME'])) : '';
			$row['ENDTIME'] = $row['ENDTIME'] > 0 ? date('Y-m-d H:i', strtotime($row['ENDTIME'])) : '';
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_exam', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_exam', $data, "EXAMID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_exam', "EXAMID = '{$id}'");
		$db->delete('tbl_exam_category', "EXAMID = '{$id}'");
		$db->delete('tbl_exam_award', "EXAMID = '{$id}'");
		$db->delete('tbl_exam_option', "EXAMID = '{$id}'");
		$db->delete('tbl_exam_user', "EXAMID = '{$id}'");
		$db->delete('tbl_exam_record', "EXAMID = '{$id}'");
	}
	
	//模块格式化
	public function module_format($exam){
		$tips = $exam['TITLE'].'；'.$GLOBALS['lang']['exam.module.view.tips'];
			
		if($exam['BEGINTIME'] > 0 || $exam['ENDTIME'] > 0){
			if($exam['BEGINTIME'] > 0) $tips .= date('Y-m-d H:i', strtotime($exam['BEGINTIME'])).$GLOBALS['lang']['exam.module.view.tips.begin'];
			if($exam['BEGINTIME'] > 0 && $exam['ENDTIME'] > 0) $tips .= ' '.$GLOBALS['lang']['exam.module.view.tips.to'].' ';
			if(endtime) $tips .= date('Y-m-d H:i', strtotime($exam['ENDTIME'])).$GLOBALS['lang']['exam.module.view.tips.end'];
		}else{
			$tips .= $GLOBALS['lang']['exam.module.view.tips.open'];
		}
		
		if($exam['GUEST']) $tips .= '；'.$GLOBALS['lang']['exam.module.view.tips.guest'];
		if($exam['RAND']) $tips .= '；'.$GLOBALS['lang']['exam.module.view.tips.rand'];
		if($exam['ISAWARD']) $tips .= '；'.$GLOBALS['lang']['exam.module.view.tips.isaward'];
		
		$exam['TIPS'] = $tips;
		
		return $exam;
	}
	
	//模块扩展
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 2){
			$module_data['identity'] = $tmparr[0];
			$module_data['examid'] = $tmparr[1];
		}else{
			$module_data['identity'] = 'exam';
		}
		
		$exam_list = $this->get_list(0, 0);
		foreach($exam_list as $key => $exam){
			$exam = $this->module_format($exam);
			$exam_list[$key]['TIPS'] = $exam['TIPS'];
			
			unset($tips);
		}
		
		include_once view('/module/exam/view/module');
	}
	
}
?>