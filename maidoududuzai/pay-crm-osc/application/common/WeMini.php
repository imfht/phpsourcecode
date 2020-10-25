<?php

namespace app\common;

use \think\Db;
use \app\common\Pay;

import('WeChat.include', EXTEND_PATH, '.php');

Class WeMini {

	//protected $sdk;

	public $config = [];

	public $appid;
	public $appsecret;

	public function __construct($Method = null, $config = [])
	{

		/* sys
		$wemini_config = model('\app\common\model\Config')->config(null, 'wemini_');
		$this->config = [
			'appid' => $wemini_config['wemini_appid'],
			'appsecret' => $wemini_config['wemini_appsecret'],
		];
		*/
		$this->config = [
			'appid' => '',
			'appsecret' => '',
		];

		//load_config
		if($config) {
			$this->set($config);
		}
		//load_method
		if($Method) {
			$this->load($Method);
		}

	}

	public function set($config = [])
	{
		//this_config
		//$this->config = $config;
		foreach($config as $key => $val) {
			$this->config[$key] = $val;
		}
	}

	public function Poi()
	{
		return $this->load(__FUNCTION__);
	}

	public function Total()
	{
		return $this->load(__FUNCTION__);
	}

	public function Image()
	{
		return $this->load(__FUNCTION__);
	}

	public function Crypt()
	{
		return $this->load(__FUNCTION__);
	}

	public function Plugs()
	{
		return $this->load(__FUNCTION__);
	}

	public function Qrcode()
	{
		return $this->load(__FUNCTION__);
	}

	public function Newtmpl()
	{
		return $this->load(__FUNCTION__);
	}

	public function Message()
	{
		return $this->load(__FUNCTION__);
	}

	public function test()
	{
		//test
	}

	public function load($Method = null)
	{

		if(empty($this->config['cache_path'])) {
			$this->config['cache_path'] = TEMP_PATH . 'WeChat';
		}
		$config = [
			'appid' => $this->config['appid'],
			'appsecret' => $this->config['appsecret'],
			'cache_path' => $this->config['cache_path'],
		];
		$Type = 'WeMini';
		$Type_Method = '\\' . $Type . '\\' . $Method;
		if(empty($Type) || empty($Method) || !class_exists($Type_Method)) {
			return null;
		} else {
			return new $Type_Method($config);
		}

	}

}

