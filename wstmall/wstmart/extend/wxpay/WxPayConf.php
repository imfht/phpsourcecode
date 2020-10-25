<?php

/**
 * 	配置账号信息
 */

class WxPayConf {
	
	static public $APPID;
	static public $APPSECRET;
	static public $MCHID;
	static public $KEY;
	static public $CURL_TIMEOUT;
	
	static public $SSLCERT_PATH;
	static public $SSLKEY_PATH;
	static public $SSLCA_PATH;
	
	static public $NOTIFY_URL;
	static public $RETURN_URL;
	// =======【基本信息设置】=====================================
	public function __construct($wxpayconfig = array()) {
	
		self::$APPID = $wxpayconfig['appid'];
		self::$APPSECRET = $wxpayconfig['appsecret'];
		self::$MCHID = $wxpayconfig['mchid'];
		self::$KEY = $wxpayconfig['key'];

		self::$SSLCERT_PATH = isset($wxpayconfig['apiclient_cert'])?$wxpayconfig['apiclient_cert']:"";
		self::$SSLKEY_PATH = isset($wxpayconfig['apiclient_key'])?$wxpayconfig['apiclient_key']:"";
		//self::$SSLCA_PATH = WSTRootPath().'/extend/wxpay/cert/rootca.pem';

		self::$CURL_TIMEOUT = $wxpayconfig['curl_timeout'];
		self::$NOTIFY_URL = $wxpayconfig['notifyurl'];
		self::$RETURN_URL = $wxpayconfig['returnurl'];
	
	}
}

?>