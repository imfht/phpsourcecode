<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

use wx\model\_wx;

/**
 * 消息
 * @author sigmazel
 * @since v1.0.2
 */
class _message{
	//搜索
	public function search(){
		global $_var;
		
		$querystring = '';
		$wheresql = ' ';
		
		if($_var['gp_txtBeginDate']) {
			$querystring .= '&txtBeginDate='.$_var['gp_txtBeginDate'];
			$wheresql .= " AND a.EDITTIME >= '{$_var[gp_txtBeginDate]}'";
		}
		
		if($_var['gp_txtEndDate']) {
			$querystring .= '&txtEndDate='.$_var['gp_txtEndDate'];
			$wheresql .= " AND a.EDITTIME <= '{$_var[gp_txtEndDate]}'";
		}
		
		if($_var['gp_txtMinRecords']) {
			$querystring .= '&txtMinRecords='.$_var['gp_txtMinRecords'];
			$wheresql .= " AND a.RECORDS >= '{$_var[gp_txtMinRecords]}'";
		}
		
		if($_var['gp_txtMaxRecords']) {
			$querystring .= '&txtMaxRecords='.$_var['gp_txtMaxRecords'];
			$wheresql .= " AND a.RECORDS <= '{$_var[gp_txtMaxRecords]}'";
		}
		
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$_var['gp_sltType'] = $_var['gp_sltType'] + 0;
			
			if($_var['gp_sltType'] == 0) $wheresql .= " AND CONCAT(a.TITLE, a.MESSAGE, a.USERNAME) LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 1) $wheresql .= " AND a.TITLE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 2) $wheresql .= " AND a.MESSAGE LIKE '%{$_var[gp_txtKeyword]}%'";
			elseif ($_var['gp_sltType'] == 3) $wheresql .= " AND a.USERNAME LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_message a WHERE a.MESSAGEID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_message a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.EDITTIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_message a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获得系统消息数量
	public function get_system_count($userid){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_message a WHERE a.READID = 0 AND a.MESSAGEID NOT IN(SELECT MESSAGEID FROM tbl_message_record b WHERE b.USERID = '{$userid}')") + 0;
	}
	
	//获取系统消息列表
	public function get_system_list($userid){
		global $db;
		
		$tempids = array();
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_message a WHERE a.READID = 0 ORDER BY a.EDITTIME DESC");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$tempids[] = $row['MESSAGEID'];
			$rows[] = $row;
		}
		
		$temp_query = $db->query("SELECT * FROM tbl_message_record a WHERE a.MESSAGEID IN(".eimplode($tempids).") AND a.USERID = '{$userid}'");
		while(($row = $db->fetch_array($temp_query)) !== false){
			foreach($rows as $key => $item){
				if($item['MESSAGEID'] == $row['MESSAGEID']){
					$rows[$key]['STATUS'] = $row['TYPE'] + 1;
					break;
				}
			}
		}
		
		return $rows;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_message', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_message', $data, "MESSAGEID = '{$id}'");
	}
	
	//删除消息-单条
	public function delete($message){
		global $db, $_var;
		
		if($message['READID'] == 0){
			$mrecord = $db->fetch_first("SELECT * FROM tbl_message_record WHERE MESSAGEID = '{$message[MESSAGEID]}' AND USERID = '{$_var[current][USERID]}'");
			if(!$mrecord){
				$db->insert('tbl_message_record', array(
				'TYPE' => 1, 
				'USERID' => $_var['current']['USERID'], 
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'MESSAGEID' => $message['MESSAGEID']
				));
			}else{
				$db->query("UPDATE tbl_message_record SET TYPE = 1 WHERE MESSAGE_RECORDID = '{$mrecord[MESSAGE_RECORDID]}'");
			}
		}else{
			$db->delete('tbl_message', "MESSAGEID = '{$message[MESSAGEID]}'");
		}
	}
	
	//清除消息-多条包括系统消息
	public function clear($message){
		global $db;
		
		$db->query("DELETE FROM tbl_message WHERE MESSAGEID = '{$message[MESSAGEID]}'");
		$db->query("DELETE FROM tbl_message WHERE PARENTID = '{$message[MESSAGEID]}'");
		$db->query("DELETE FROM tbl_message_record WHERE MESSAGEID = '{$message[MESSAGEID]}'");
	}
	
	//读取消息
	public function read($message){
		global $db, $_var;
		
		if($message['READID'] == 0){
			$mrecord = $db->fetch_first("SELECT * FROM tbl_message_record WHERE MESSAGEID = '{$message[MESSAGEID]}' AND USERID = '{$_var[current][USERID]}'");
			
			if(!$mrecord){
				$db->insert('tbl_message_record', array(
				'TYPE' => 0, 
				'USERID' => $_var['current']['USERID'], 
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s'), 
				'MESSAGEID' => $message['MESSAGEID']
				));
				
				$db->query("UPDATE tbl_message SET RECORDS = RECORDS + 1 WHERE MESSAGEID = '{$message[MESSAGEID]}'");
			}
		}elseif($message['STATUS'] == 0){
			$db->update('tbl_message', array('STATUS' => 1, 'READTIME' => date('Y-m-d H:i:s'), 'RECORDS' => 1), "MESSAGEID = '{$message[MESSAGEID]}'");
		}
	}
	
	//发送消息
	public function send($message){
		if($message['TEMPLATE'] == 'WX'){
			$result = $this->send_of_wx($message);
			return $result ? '200' : '100';
		}
		
		if($message['TEMPLATE'] == 'EMAIL'){
			$result = sendmail($message['EMAIL'], $message['TITLE'], $message['MESSAGE']);
			return $result ? '200' : '100';
		}
		
		if($message['TEMPLATE'] == 'SMS'){
			$_sms = new _sms();
			
			$result = $_sms->send($message['MOBILE'], utf8substr($message['MESSAGE'], 0, 70));
			return $result == 0 ? '200' : '100';
		}
		
		return '200';
	}
	
	//发送微信消息
	public function send_of_wx($message){
		global $db, $setting;
		
		$_wx = new _wx();
		
		$msgtpl = $db->fetch_first("SELECT * FROM tbl_message_tpl WHERE SERIAL = '{$message[serial]}'");
		if(!$msgtpl || !$msgtpl['ENABLED']) return false;
		
		$msgdata = array();
		$temparr = explode("\n", $msgtpl['REMARK']);
		foreach($temparr as $key => $val){
			$val = str_replace('：', ':', $val);
			$tmp = explode('.DATA}}:', $val);
			if(count($tmp) != 2) continue;
			$msgdata[str_replace('{{', '', $tmp[0])] = $tmp[1];
		}
		
		if($message['serial'] == 'OPENTM201843398' || $message['serial'] == 'OPENTM201843387'){
			$template = array(
			'touser' => $message['touser'],
			'template_id' => $msgtpl['IDENTITY'],
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $message['first'] ? $message['first'] : $msgdata['first'],
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $message['keyword1'],
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => $message['keyword2'],
					'color' => '#173177'
				),
				'keyword3' => array(
					'value' => $message['keyword3'],
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $message['remark'] ? $message['remark'] : $msgdata['remark'],
					'color' => '#173177'
				)
			));
		}elseif($message['serial'] == 'OPENTM202243318' || $message['serial'] == 'OPENTM201014137'){
			$template = array(
			'touser' => $message['touser'],
			'template_id' => $msgtpl['IDENTITY'],
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $message['first'] ? $message['first'] : $msgdata['first'],
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $message['keyword1'],
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => $message['keyword2'],
					'color' => '#173177'
				),
				'keyword3' => array(
					'value' => $message['keyword3'],
					'color' => '#173177'
				),
				'keyword4' => array(
					'value' => $message['keyword4'],
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $message['remark'] ? $message['remark'] : $msgdata['remark'],
					'color' => '#173177'
				)
			));
		}elseif($message['serial'] == 'OPENTM204658409'){
			$template = array(
			'touser' => $message['touser'],
			'template_id' => $msgtpl['IDENTITY'],
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $message['first'] ? $message['first'] : $msgdata['first'],
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $message['keyword1'],
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => $message['keyword2'],
					'color' => '#173177'
				),
				'keyword3' => array(
					'value' => $message['keyword3'],
					'color' => '#173177'
				),
				'keyword4' => array(
					'value' => $message['keyword4'],
					'color' => '#173177'
				),
				'keyword5' => array(
					'value' => $message['keyword5'],
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $message['remark'] ? $message['remark'] : $msgdata['remark'],
					'color' => '#173177'
				)
			));
		}elseif($message['serial'] == 'OPENTM204650588' || $message['serial'] == 'OPENTM200681790'){
			$template = array(
			'touser' => $message['touser'],
			'template_id' => $msgtpl['IDENTITY'],
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $message['first'] ? $message['first'] : $msgdata['first'],
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $message['keyword1'],
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => $message['keyword2'],
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $message['remark'] ? $message['remark'] : $msgdata['remark'],
					'color' => '#173177'
				)
			));
		}elseif($message['serial'] == 'TM00004'){
			$template = array(
			'touser' => $message['touser'],
			'template_id' => $msgtpl['IDENTITY'],
			'topcolor' => '#FF0000',
			'data' => array(
				'first' => array(
					'value' => $message['first'] ? $message['first'] : $msgdata['first'],
					'color' => '#173177'
				),
				'reason' => array(
					'value' => $message['reason'],
					'color' => '#173177'
				),
				'refund' => array(
					'value' => $message['refund'],
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $message['remark'] ? $message['remark'] : $msgdata['remark'],
					'color' => '#173177'
				)
			));
		}
		
		if($message['url']){
			$template['url'] = substr($message['url'], 0, 7) == 'http://' || substr($message['url'], 0, 8) == 'https://' ? $message['url'] : $setting['SiteHost'].$message['url'];
		}
		
		$wx_setting = $message['WX_SETTING'];
		$access_token = $_wx->token($wx_setting['WX_APPID'], $wx_setting['WX_SECRET']);
		
		$return = $_wx->request("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}", json_encode($template), 'POST');
		$return = json_decode($return, 1);
		
		return $return['errmsg'] == 'ok';
	}

}
?>