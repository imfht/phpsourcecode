<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

//参数
class _setting{
	//读取
	public function get($key = ''){
		global $db;
		
		$temparr = array();
		
		$temp_query = $db->query("SELECT * FROM tbl_setting ".($key ? "WHERE SKEY = '{$key}'" : ''));
		while(($row = $db->fetch_array($temp_query)) !== false){
			$temparr[$row['SKEY']] = $row['SVALUE'];
		}
		
		return $temparr;
	}
	
	//写入
	public function set($key, $val){
		global $db;
		
		$db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('{$key}', '{$val}')");
	}
	
	//格式化
	public function format($setting){
		global $config;
		
		//模板
		$setting['SiteTheme'] = $setting['SiteTheme'];
		$setting['SiteTheme'] = substr($setting['SiteTheme'], 0, 1) == '/' ? substr($setting['SiteTheme'], 1) : $setting['SiteTheme'];
		$setting['SiteTheme'] = substr($setting['SiteTheme'], -1) == '/' ? substr($setting['SiteTheme'], 0, -1) : $setting['SiteTheme'];
		
		!$setting['Application'] && $setting['Application'] = $config['app'];
		
		//支付方式
		$setting['MarketPay'] = unserialize($setting['MarketPay']);
		//商品属性
		$setting['ProductPros'] = unserialize($setting['ProductPros']);
		//商品价格参数
		$setting['ProductPrices'] = unserialize($setting['ProductPrices']);
		//商品规格参数
		$setting['MarketMatchs'] = unserialize($setting['MarketMatchs']);
		//物流方式
		$setting['MarketDeliver'] = unserialize($setting['MarketDeliver']);
		//物流阶梯运费
		$setting['MarketDeliverPrice'] = unserialize($setting['MarketDeliverPrice']);
		//套餐发货日期
		$setting['MarketSuitSend'] = unserialize($setting['MarketSuitSend']);
		
		return $setting;
	}
	
	//检查许可IP
	public function check_allow_ip(){
	    global $_var, $setting;
	    
	    //不存在变量，返回
	    if(!$setting['ALLOWIP']) return true;
	    if(!strexists("#{$setting[ALLOWIP]}#", "#{$_var[clientip]}#")) return false;
	    
	    return true;
	}
	
	//检查禁止IP
	public function check_limit_ip(){
		global $_var, $setting;
		
		//不存在变量，返回
	    if(!$setting['NOIP']) return false;
	    if(strexists("{$setting[NOIP]}", "#{$_var[clientip]}#")) return true;
	    
	    return false;
	}
	
	//检查站点是否关闭
	public function check_closed(){
		global $setting;
		
		return $setting['SiteIsClosed'];
	}
	
}
?>