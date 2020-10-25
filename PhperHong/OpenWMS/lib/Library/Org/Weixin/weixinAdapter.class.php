<?php
	namespace Org\Weixin;
	use Think\Log;
	use Think\Cache;
	require_once dirname(__FILE__) . '/weixin.class.php';
	class weixinAdapter {
		private $auth;
		private $cache;
		private $host='https://api.weixin.qq.com/wifi/';
		function __construct($client_id, $client_secret) {
			$this->cache   = Cache::getInstance();
			$this->auth = new \WeixinTOAuth($client_id, $client_secret);
		}
		public function get_access_token(){
			$at = $this->cache->get('weixin_access_token');

			if ($at == ''){
				$access_token = $this->auth->getAccessToken();
				
				
				if (!is_array($access_token) || isset($access_token['errmsg'])){
					Log::record('微信token获取失败：'.json_encode($access_token) );
					return false;
				}
			
				$at = $access_token['access_token'];
				
				$this->cache->set('weixin_access_token', $at, $access_token['expires_in']);




			}
			
			return $at;
		}
		/*
		* 获取wifi二维码
		*/
		public function get_qr_code($access_token, $ssid, $storeId, $bgUrl='', $lgnId='', $templateId='0', $storeName='', $expireTime=''){

			$parameters = array(
				'access_token'	=> $access_token,
				'format'		=> 'json',
				'ssid'			=> $ssid,
				'storeId'		=> $storeId,
				'lgnId'			=> $lgnId,
				'templateId'	=> $templateId,
				'bgUrl'			=> $bgUrl,
				'storeName'		=> $storeName,
				'expireTime'	=> $expireTime,

			);

			return $this->auth->post($this->host.'getQRCode.xhtml', $parameters);

		}
		/**
		* 设置回调函数及token
		*/
		public function set_vendor_config($access_token, $callbackurl, $token){
			//https://api.weixin.qq.com/wifi/setVendorConfig.xhtml?access_token=ACCESSTOKEN&format=json
			$parameters = array(
				'access_token'	=> $access_token,
				'format'		=> 'json',
				'callbackUrl'	=> $callbackurl,
				'token'			=> $token,
			);

			return $this->auth->post($this->host.'setVendorConfig.xhtml', $parameters);
		}
		/**
		* 校验授权码
		*/
		public function checkSignature($signature, $timestamp, $nonce, $token){
			
			$tmpArr = array($token, $timestamp, $nonce);
			sort($tmpArr, SORT_STRING);
			$tmpStr = implode( $tmpArr );
			$tmpStr = sha1( $tmpStr );
			if( $tmpStr != $signature ){
				return false;
			}
			return true;
		}
		/**
		* 通知微信
		*/
		public function notice_weixin_info($access_token, $action, $mac){
			return $this->auth->get($this->host.'setVendorConfig.xhtml?access_token='.$access_token.'&format=json&action='.$action.'&mac='.$mac);
		}
	}
