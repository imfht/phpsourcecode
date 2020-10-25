<?php
//版权所有(C) 2014 www.ilinei.com

namespace user\model;

class _credit_online{
	public function get_serial(){
		global $db;
		
		$temp = $db->fetch_first("SELECT * FROM tbl_setting WHERE SKEY = 'CreditOnlineSerial'");
		if($temp){
			$temp['SVALUE'] = $temp['SVALUE'] + 1;
			$db->update('tbl_setting', array('SVALUE' => $temp['SVALUE']), "SKEY = 'CreditOnlineSerial'");
		}else{
			$temp['SVALUE'] = 1;
			$db->insert('tbl_setting', array('SKEY' => 'CreditOnlineSerial', 'SVALUE' => $temp['SVALUE']));
		}
		
		return 'CO'.substr(date('Ym'), 3).sprintf('%07d', $temp['SVALUE']);
	}
	
	public function get_by_id($id){
		global $db;
		
		return $db->fetch_first("SELECT a.* FROM tbl_credit_online a WHERE a.CREDIT_ONLINEID = '{$id}'");
	}
	
	public function insert($data){
		global $db;
		
		$db->insert('tbl_credit_online', $data);
		
		return $db->insert_id();
	}
	
	public function update($id, $data){
		global $db;
		
		$db->update('tbl_credit_online', $data, "CREDIT_ONLINEID = '{$id}'");
	}

}
?>