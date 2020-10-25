<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\model;

/**
 * 微信消息
 * @author sigmazel
 * @since v1.0.2
 */
class _wx_msg{
	//搜索
	public function search(){
		global $_var;
	
		$querystring = '';
		$wheresql = '';
	
		if($_var['gp_txtKeyword']) {
			$_var['gp_txtKeyword'] = trim($_var['gp_txtKeyword']);
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			
			$wheresql .= " AND a.MSGTYPE = 'text' AND CONCAT(a.TITLE, a.CONTENT, IFNULL(b.NICKNAME, '')) LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		if($_var['gp_hdnSearchShow']) $querystring .= '&hdnSearchShow='.$_var['gp_hdnSearchShow'];
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_wx_msg a WHERE a.WX_MSGID = '{$id}'");
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_wx_msg a WHERE 1 {$wheresql}") + 0;
		
	}
	
	//获取列表
	public function get_list($start, $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		$rows = array();
		
		!$ordersql && $ordersql = "ORDER BY a.CREATETIME DESC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$temp_query = $db->query("SELECT a.* FROM tbl_wx_msg a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//添加
	public function insert($postObj){
		global $db;
		
		$db->insert('tbl_wx_msg', array(
		'MSGID' => $postObj['MsgId'], 
		'TOUSERNAME' => $postObj['ToUserName'], 
		'FROMUSERNAME' => $postObj['FromUserName'], 
		'CREATETIME' => $postObj['CreateTime'], 
		'MSGTYPE' => $postObj['MsgType'], 
		'LOCATIONX' => $postObj['Location_X'], 
		'LOCATIONY' => $postObj['Location_Y'], 
		'SCALE' => $postObj['Scale'], 
		'LABEL' => $postObj['Label'], 
		'TITLE' => $postObj['Title'], 
		'DESCRIPTION' => $postObj['Description'], 
		'URL' => $postObj['Url'], 
		'PICURL' => $postObj['PicUrl'], 
		'CONTENT' => $postObj['Content'], 
		'MEDIAID' => $postObj['MediaId'], 
		'FORMAT' => $postObj['Format'], 
		'RECONGNITION' => $postObj['Recognition']
		));
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_wx_msg', $data, "WX_MSGID = '{$id}'");
	}
	
	//删除
	public function delete($where = ''){
		global $db;
		
		$db->delete('tbl_wx_msg', $where);
	}
	
}
?>