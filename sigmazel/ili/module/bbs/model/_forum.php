<?php
//版权所有(C) 2014 www.ilinei.com

namespace bbs\model;

/**
 * 讨论版
 * @author sigmazel
 * @since v1.0.2
 */
class _forum{
	//搜索 
	public function search(){
		global $_var;
		
		$querystring = $wheresql = '';
		
		if($_var['gp_txtKeyword']) {
			$querystring .= '&txtKeyword='.$_var['gp_txtKeyword'];
			$wheresql .= " AND CONCAT(a.NAME, a.RULE, a.REMARK) LIKE '%{$_var[gp_txtKeyword]}%'";
		}
		
		return array('querystring' => $querystring, 'wheresql' => $wheresql);
	}
	
	//表情
	public function get_faces(){
		$faces[1] = array('{微笑}','{撇嘴}','{色}','{发呆}','{得意}','{流泪}','{害羞}','{闭嘴}','{睡}','{大哭}','{尴尬}','{发怒}','{调皮}','{呲牙}','{惊讶}','{难过}','{酷}','{冷汗}','{抓狂}','{吐}', '');
		$faces[2] = array('{偷笑}','{可爱}','{白眼}','{傲慢}','{饥饿}','{困}','{惊恐}','{流汗}','{憨笑}','{大兵}','{奋斗}','{咒骂}','{疑问}','{嘘}','{晕}','{折磨}','{衰}','{骷髅}','{敲打}','{再见}', '');
		$faces[3] = array('{擦汗}','{抠鼻}','{鼓掌}','{糗大了}','{坏笑}','{左哼哼}','{右哼哼}','{哈欠}','{鄙视}','{委屈}','{快哭了}','{阴险}','{亲亲}','{吓}','{可怜}','{菜刀}','{西瓜}','{啤酒}','{篮球}','{乒乓}', '');
		$faces[4] = array('{咖啡}','{饭}','{猪头}','{玫瑰}','{凋谢}','{示爱}','{爱心}','{心碎}','{蛋糕}','{闪电}','{炸弹}','{刀}','{足球}','{瓢虫}','{便便}','{月亮}','{太阳}','{礼物}','{拥抱}','{强}','');
		$faces[5] = array('{弱}','{握手}','{胜利}','{抱拳}','{勾引}','{拳头}','{差劲}','{爱你}','{NO}','{OK}','{爱情}','{飞吻}','{跳跳}','{发抖}','{怄火}','{转圈}','{磕头}','{回头}','{跳绳}','{挥手', '');
		
		return $faces;
	}
	
	//格式化表情
	public function format_face($content){
		$faces_list = $this->get_faces();
		
		foreach($faces_list as $key => $faces){
			foreach($faces as $fkey => $face){
				$content = str_replace($face, "<img class=\"face\" src=\"tpl/_res/mobile/images/face/icon-face-{$key}-{$fkey}.png\"/>", $content);
			}
		}
		
		return $content;
	}
	
	//根据ID获取记录
	public function get_by_id($id){
		global $db;
		
		$forum = $db->fetch_first("SELECT * FROM tbl_forum WHERE FORUMID = '{$id}'");
		if($forum) $forum = format_row_files($forum);
		
		return $forum;
	}
	
	//获取数量
	public function get_count($wheresql = ''){
		global $db;
	
		return $db->result_first("SELECT COUNT(1) FROM  tbl_forum a WHERE 1 {$wheresql}") + 0;
	}
	
	//获取列表
	public function get_list($start , $perpage, $wheresql = '', $ordersql = ''){
		global $db;
		
		!$ordersql && $ordersql = "ORDER BY a.DISPLAYORDER ASC";
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
	
		$temp_query = $db->query("SELECT a.* FROM tbl_forum a WHERE 1 {$wheresql} {$ordersql} {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row = format_row_files($row);
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	//获取第一条记录
	public function get_first($wheresql = ''){
		global $db;
		
		return $db->fetch_first("SELECT * FROM tbl_forum a WHERE 1 {$wheresql} ORDER BY a.DISPLAYORDER ASC LIMIT 0, 1");
	}
	
	//获取等级列表
	public function get_groups($groupids){
		global $db;
		
		$groups = array();
		
		$tmparr = explode(',', $groupids);
		$temparr = array();
		foreach ($tmparr as $key => $val) {
			if($val && !in_array($val, $temparr)) $temparr[] = $val + 0;
		}
		
		if(count($temparr) > 0){
			$temp_query = $db->query("SELECT * FROM tbl_group WHERE GROUPID IN(".eimplode($temparr).")");
			while(($row = $db->fetch_array($temp_query)) !== false){
				$groups[$row['GROUPID']] = $row;
			}
		}
		
		return $groups;
	}
	
	//获取统计
	public function get_stat($forum){
		global $db;
		
		$forum['TOPICS'] = $db->result_first("SELECT COUNT(1) FROM tbl_forum_topic WHERE FORUMID = '{$forum[FORUMID]}'") + 0;
		$forum['POSTS'] = $db->result_first("SELECT COUNT(1) FROM tbl_forum_post WHERE FIRST = 0 AND FORUMID = '{$forum[FORUMID]}'") + 0;
		
		return $forum;
	}
	
	//添加
	public function insert($data){
		global $db;
		
		$db->insert('tbl_forum', $data);
		
		return $db->insert_id();
	}
	
	//修改
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_forum', $data, "FORUMID = '{$id}'");
	}
	
	//删除
	public function delete($id){
		global $db;
		
		$db->delete('tbl_forum', "FORUMID = '{$id}'");
		$db->delete('tbl_forum_topic', "FORUMID = '{$id}'");
		$db->delete('tbl_forum_post', "FORUMID = '{$id}'");
		$db->delete('tbl_forum_user', "FORUMID = '{$id}'");
	}

}
?>