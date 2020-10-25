<?php
//版权所有(C) 2014 www.ilinei.com

namespace poll\model;

/**
 * 投票
 * @author sigmazel
 * @since v1.0.2
 */
class _poll{
	//获取状态值
	public function get_status(){
		return array(
		0 => '未启用', 
		1 => '已启用'
		);
	}
	
	//根据ID获取记录
	public function get_by_id($pollid){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_poll WHERE POLLID = '{$pollid}'");
	}
	
	//根据标识号获取记录
	public function get_by_identity($identity){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_poll WHERE IDENTITY = '{$identity}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_poll a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db, $setting;
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		$temp_query = $db->query("SELECT a.* FROM tbl_poll a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			
			$row['BEGINTIME'] = $row['BEGINTIME'] > 0 ? date('Y-m-d H:i', strtotime($row['BEGINTIME'])) : '';
			$row['ENDTIME'] = $row['ENDTIME'] > 0 ? date('Y-m-d H:i', strtotime($row['ENDTIME'])) : '';
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加+项目
	public function add($poll){
		global $db;
		
		$db->insert('tbl_poll', array(
		'TITLE' => $poll['TITLE'], 
		'IDENTITY' => $poll['IDENTITY'], 
		'SUMMARY' => $poll['SUMMARY'], 
		'CONTENT' => $poll['CONTENT'], 
		'STATUS' => $poll['STATUS'], 
		'GUEST' => $poll['GUEST'],
		'OPTIONTYPE' => $poll['OPTIONTYPE'], 
		'SIMPLE' => $poll['SIMPLE'], 
		'LIMITNUM' => $poll['LIMITNUM'], 
		'BEGINTIME' => $poll['BEGINTIME'], 
		'ENDTIME' => $poll['ENDTIME'], 
		'USERID' => $poll['USER']['USERID'],
		'USERNAME' => $poll['USER']['WX_FANSID'] ? $poll['USER']['REALNAME'] : $poll['USER']['USERNAME'],
		'EDITTIME' => date('Y-m-d H:i:s')
		));
		
		$pollid = $db->insert_id();
		
		foreach($poll['OPTIONS'] as $key => $option){
			$db->insert('tbl_poll_option', array(
			'POLLID' => $pollid, 
			'TITLE' => $option, 
			'DISPLAYORDER' => $key + 1, 
			'VOTES' => 0, 
			'USERID' => $poll['USER']['USERID'], 
			'USERNAME' => $poll['USER']['WX_FANSID'] ? $poll['USER']['REALNAME'] : $poll['USER']['USERNAME'], 
			'EDITTIME' => date('Y-m-d H:i:s'), 
			'STATUS' => 1
			));
		}
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_poll', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_poll', $data, "POLLID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_poll', " POLLID = '{$id}'");
		$db->delete('tbl_poll_award', " POLLID = '{$id}'");
		$db->delete('tbl_poll_option', " POLLID = '{$id}'");
		$db->delete('tbl_poll_vote', " POLLID = '{$id}'");
	}
	
	//模块扩展
	public function module($module){
		$module_data = array();
		$tmparr = explode('|', $module);
		
		if(count($tmparr) >= 2){
			$module_data['identity'] = $tmparr[0];
			$module_data['pollid'] = $tmparr[1];
		}else{
			$module_data['identity'] = 'poll';
		}
		
		$poll_list = $this->get_list(0, 0);
		foreach($poll_list as $key => $poll){
			$poll['NEEDS'] = serialize(	$poll['NEEDS']);
			$poll = $this->module_format($poll);
			
			$poll_list[$key]['TIPS'] = $poll['TIPS'];
			
			unset($tips);
		}
		
		include_once view('/module/poll/view/module');
	}
	
	//模块格式化
	public function module_format($poll){
		$tips = $poll['TITLE'].'；'.$GLOBALS['lang']['poll.module.view.tips'];
			
		if($poll['BEGINTIME'] > 0 || $poll['ENDTIME'] > 0){
			if($poll['BEGINTIME'] > 0) $tips .= date('Y-m-d H:i', strtotime($poll['BEGINTIME'])).$GLOBALS['lang']['poll.module.view.tips.begin'];
			if($poll['BEGINTIME'] > 0 && $poll['ENDTIME'] > 0) $tips .= ' '.$GLOBALS['lang']['poll.module.view.tips.to'].' ';
			if(endtime) $tips .= date('Y-m-d H:i', strtotime($poll['ENDTIME'])).$GLOBALS['lang']['poll.module.view.tips.end'];
		}else{
			$tips .= $GLOBALS['lang']['poll.module.view.tips.open'];
		}
		
		if($poll['GUEST']) $tips .= '；'.$GLOBALS['lang']['poll.module.view.tips.guest'];
		if($poll['RAND']) $tips .= '；'.$GLOBALS['lang']['poll.module.view.tips.rand'];
		if($poll['ISAWARD']) $tips .= '；'.$GLOBALS['lang']['poll.module.view.tips.isaward'];
		
		$tips = substr($tips, -1) == ',' ? substr($tips, 0, -1) : $tips;
		
		$poll['TIPS'] = $tips;
		
		return $poll;
	}

}
?>