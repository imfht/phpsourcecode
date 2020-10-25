<?php
//版权所有(C) 2014 www.ilinei.com

namespace wx\model;

/**
 * 微信参数配置
 * @author sigmazel
 * @since v1.0.2
 */
class _wx_setting{
	//读取
	public function get($key = ''){
		global $db;
	
		$temparr = array();
	
		$temp_query = $db->query("SELECT * FROM tbl_wx_setting ".($key ? "WHERE SKEY = '{$key}'" : ''));
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[$row['SKEY']] = $row['SVALUE'];
		}
	
		return $temparr;
	}
	
	public function set($wx_setting, $skey, $svale){
		if(isset($wx_setting[$skey])) $this->update($skey, $svale);
		else $this->insert($skey, $svale);
	}
	
	//添加
	public function insert($skey, $svalue){
		global $db;
		
		$db->insert('tbl_wx_setting', array('SKEY' => $skey, 'SVALUE' => $svalue));
	}
	
	//更新
	public function update($skey, $svalue){
		global $db;
		
		$db->update('tbl_wx_setting', array('SVALUE' => $svalue), "SKEY = '{$skey}'");
	}
	
	//格式化
	public function format($wx_setting){
		global $setting;
		
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_CERT');
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_KEY');
		$wx_setting = format_row_file($wx_setting, 'WX_PEM_ROOTCA');
		
		//微信API配置
		$wx_setting['APPID'] = $wx_setting['WX_APPID'];
		$wx_setting['APPSECRET'] = $wx_setting['WX_SECRET'];
		$wx_setting['MCHID'] = $wx_setting['WX_PARTNERID'];
		$wx_setting['KEY'] = $wx_setting['WX_PARTNERKEY'];
		$wx_setting['SSLCERT_PATH'] = $wx_setting['WX_PEM_CERT'][3];
		$wx_setting['SSLKEY_PATH'] = $wx_setting['WX_PEM_KEY'][3];
		$wx_setting['JS_API_CALL_URL'] = "{$setting[SiteHost]}wx/pay.do";
		$wx_setting['NOTIFY_URL'] = "{$setting[SiteHost]}wx/notify.do";
		$wx_setting['CURL_TIMEOUT'] = 30;
		
		return $wx_setting;
	}
	
}
?>