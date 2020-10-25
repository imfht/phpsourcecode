<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

class _credit_record{
	public function get_count($where){
		global $db;
		
		return $db->result_first("SELECT COUNT(1) FROM tbl_credit_record a WHERE 1 {$where}");
	}
	
	public function get_list($start, $perpage, $where){
		global $db;
		
		$perpage > 0 && $limitsql = "LIMIT $start, $perpage";
		
		$rows = array();
		
		$temp_query = $db->query("SELECT a.* FROM tbl_credit_record a WHERE 1 {$where} ORDER BY a.EDITTIME DESC {$limitsql}");
		while(($row = $db->fetch_array($temp_query)) !== false){
			$row['CREDIT1'] = format_credit($row['CREDIT1']);
			$row['CREDIT2'] = format_credit($row['CREDIT2']);
			
			$rows[] = $row;
		}
		
		return $rows;
	}
	
	public function get_stat($where){
		global $db;
		
		return $db->fetch_first("SELECT SUM(CREDIT2) AS CREDIT, SUM(SCORE2) AS SCORE FROM tbl_credit_record a WHERE 1 {$where}");
	}
	
	public function insert($data){
		global $db;
		
		$db->insert('tbl_credit_record', $data);
		
		return $db->insert_id();
	}
	
	public function insert_by_rule($user, $rule, $params){
		global $db, $_var;
		
		$score = $credit = 0;
		$user['CREDIT_RECORD'] = '';
		
		if($params['SCORE']){
			if(substr($rule['SCORE'], -1) == '%') $score = round(($params['SCORE'] * (substr($rule['SCORE'], 0, -1) + 0.00)) / 100.00);
			else $score = $params['SCORE'];
		}else{
			$score = $rule['SCORE'] * $rule['NUM'];
		}
		
		if($params['CREDIT']){
			if(substr($rule['CREDIT'], -1) == '%') $credit = round(($params['CREDIT'] * (substr($rule['SCORE'], 0, -1) + 0.00)) / 100.00);
			else $credit = $params['CREDIT'];
		}else{
			$credit = $rule['CREDIT'] * $rule['NUM'];
		}
		
		$db->insert('tbl_credit_record', array(
		'TITLE' => utf8substr($rule['NAME'], 0, 50), 
		'CREDIT1' => $user['CREDIT'], 
		'CREDIT2' => $credit + 0, 
		'SCORE1' => $user['SCORE'], 
		'SCORE2' => $score + 0, 
		'ADDRESS' => $_var['clientip'], 
		'AGENT' => cutstr($_SERVER['HTTP_USER_AGENT'], 200, ''), 
		'USERID' => $user['USERID'], 
		'USERNAME' => $user['USERNAME'], 
		'EDITTIME' => date('Y-m-d H:i:s'), 
		'ABOUTTYPE' => $params['ABOUTTYPE'], 
		'ABOUTID' => $params['ABOUTID'], 
		'ACTION' => $rule['ACTION']
		));
		
		$user['SCORE'] += $score;
		$user['CREDIT'] += $credit;
		
		$user['CREDIT_RECORD'] = array('SCORE' => $score + 0, 'CREDIT' => $credit + 0);
		
		return $user;
	}

}
?>