<?php
//版权所有(C) 2014 www.ilinei.com

namespace note\model;

/**
 * 留言板
 * @author sigmazel
 * @since v1.0.2
 */
class _note{
	//搜索 
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($noteid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_note WHERE NOTEID = '{$noteid}'");
	}
	
	//根据标题获取记录
	public function get_by_title($title){
		global $db;
	
		return $db->fetch_first("SELECT * FROM tbl_note WHERE TITLE = '{$title}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_note a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_note a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['BEGINDATE'] = $row['BEGINDATE'] > 0 ? date('Y-m-d', strtotime($row['BEGINDATE'])) : '';
			$row['ENDDATE'] = $row['ENDDATE'] > 0 ? date('Y-m-d', strtotime($row['ENDDATE'])) : '';
			$row['NEEDS'] = unserialize($row['NEEDS']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_note', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_note', $data, "NOTEID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_note', "NOTEID = '{$id}'");
		$db->delete('tbl_note_record', "NOTEID = '{$id}'");
	}
	
	//模块扩展
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 2){
			$module_data['identity'] = $tmparr[0];
			$module_data['noteid'] = $tmparr[1];
		}else{
			$module_data['identity'] = 'note';
		}
		
		$note_list = $this->get_list(0, 0);
		foreach($note_list as $key => $note){
			$note['NEEDS'] = serialize(	$note['NEEDS']);
			$note = $this->module_format($note);
			
			$note_list[$key]['TIPS'] = $note['TIPS'];
			
			unset($tips);
		}
		
		include_once view('/module/note/view/module');
	}
	
	//模块格式化
	public function module_format($note){
		$tips = $note['TITLE'].'；'.$GLOBALS['lang']['note.module.view.tips'];
			
		if($note['BEGINTIME'] > 0 || $note['ENDTIME'] > 0){
			if($note['BEGINTIME'] > 0) $tips .= date('Y-m-d H:i', strtotime($note['BEGINTIME'])).$GLOBALS['lang']['note.module.view.tips.begin'];
			if($note['BEGINTIME'] > 0 && $note['ENDTIME'] > 0) $tips .= ' '.$GLOBALS['lang']['note.module.view.tips.to'].' ';
			if(endtime) $tips .= date('Y-m-d H:i', strtotime($note['ENDTIME'])).$GLOBALS['lang']['note.module.view.tips.end'];
		}else{
			$tips .= $GLOBALS['lang']['note.module.view.tips.open'];
		}
		
		if($note['GUEST']) $tips .= '；'.$GLOBALS['lang']['note.module.view.tips.guest'];
		if($note['NEEDS']){
			$tips .= '；'.$GLOBALS['lang']['note.module.view.tips.needs'];
			
			if($note['NEEDS']['department']) $tips .= $GLOBALS['lang']['note.module.view.tips.department'].',';
			if($note['NEEDS']['place']) $tips .= $GLOBALS['lang']['note.module.view.tips.place'].',';
			if($note['NEEDS']['email']) $tips .= $GLOBALS['lang']['note.module.view.tips.email'].',';
			if($note['NEEDS']['connect']) $tips .= $GLOBALS['lang']['note.module.view.tips.connect'].',';
		}
		
		$tips = substr($tips, -1) == ',' ? substr($tips, 0, -1) : $tips;
		
		$note['TIPS'] = $tips;
		
		return $note;
	}
	
}
?>