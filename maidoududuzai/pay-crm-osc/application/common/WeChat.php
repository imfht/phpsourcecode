<?php

namespace app\common;

use \think\Db;
use \app\common\Pay;

import('WeChat.include', EXTEND_PATH, '.php');

Class WeChat {

	//protected $sdk;

	public $config = [];

	public $appid;
	public $mch_id;
	public $mch_key;

	public $ssl_key;
	public $ssl_cer;

	public $token;
	public $appsecret;
	public $encodingaeskey;

	public function __construct($Method = null, $config = [])
	{

		$this->config = Pay::config('weixin');

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

	public function Menu()
	{
		return $this->load(__FUNCTION__);
	}

	public function User()
	{
		return $this->load(__FUNCTION__);
	}

	public function Card()
	{
		return $this->load(__FUNCTION__);
	}

	public function Scan()
	{
		return $this->load(__FUNCTION__);
	}

	public function Oauth()
	{
		return $this->load(__FUNCTION__);
	}

	public function Media()
	{
		return $this->load(__FUNCTION__);
	}

	public function Qrcode()
	{
		return $this->load(__FUNCTION__);
	}

	public function Script()
	{
		return $this->load(__FUNCTION__);
	}

	public function Custom()
	{
		return $this->load(__FUNCTION__);
	}

	public function Receive()
	{
		return $this->load(__FUNCTION__);
	}

	public function Template()
	{
		return $this->load(__FUNCTION__);
	}

	public function make_template($openid, $template_id, $template_info = []) {
		$color = !empty($template_info['color']) ? $template_info['color'] : '#000000';
		$data = [];
		$data['first'] = [
			'color' => $color,
			'value' => $template_info['first'],
		];
		for ($i=1; $i<9; $i++) {
			if(isset($template_info["keyword{$i}"])) {
				$data["keyword{$i}"] = [
					'color' => $color,
					'value' => $template_info["keyword{$i}"],
				];
			}
		}
		$data['remark'] = [
			'color' => '#000000',
			'value' => $template_info['remark'],
		];
		$url = '';
		if(isset($template_info['url'])) {
			$url = $template_info['url'];
		}
		$topcolor = '#000000';
		if(!empty($template_info['topcolor'])) {
			$topcolor = $template_info['topcolor'];
		}
		$template = [
			'template_id' => $template_id,
			'touser' => $openid,
			'data' => $data,
			'url' => $url,
			'topcolor' => $topcolor,
		];
		return $template;
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
			'token' => $this->config['token'],
			'appid' => $this->config['appid'],
			'appsecret' => $this->config['appsecret'],
			'encodingaeskey' => $this->config['encodingaeskey'],
			'mch_id' => $this->config['mch_id'],
			'mch_key' => $this->config['mch_key'],
			'ssl_key' => $this->config['ssl_key'],
			'ssl_cer' => $this->config['ssl_cer'],
			'cache_path' => $this->config['cache_path'],
		];
		$Type = 'WeChat';
		$Type_Method = '\\' . $Type . '\\' . $Method;
		if(empty($Type) || empty($Method) || !class_exists($Type_Method)) {
			return null;
		} else {
			return new $Type_Method($config);
		}

	}

}

