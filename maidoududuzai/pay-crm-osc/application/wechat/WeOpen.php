<?php

namespace app\wechat;

use \think\Db;

import('WeChat.include', EXTEND_PATH, '.php');

class WeOpen
{

	public $config;
	public $Service;

	public function __construct()
	{

		$config = [
			'component_appid' => 'wx4855dabe01de977e',
			'component_token' => 'Tryyun',
			'component_appsecret' => 'd6c64b8a2c62d9349b8a5dfcd5e89b0d',
			'component_encodingaeskey' => 'KQEeeDYBjixiUBlzRtANhYSvvaejMtJnQeAYHmWXFYW',
			'cache_path' => TEMP_PATH . 'WeOpen',
		];
		$this->Service = new \WeOpen\Service($config);

	}

	public function log($content = '')
	{

		Tool::log($content, 'WeOpen');

	}

}

