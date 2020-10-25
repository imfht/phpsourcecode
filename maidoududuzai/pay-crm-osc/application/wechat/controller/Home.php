<?php

namespace app\wechat\controller;

use \think\Db;
use \app\common\WeChat;
use \app\common\WeChatConsole;

class Home
{

	public function __construct()
	{

	}

	public function log($content = '')
	{

		Tool::log($content, 'WeChat');

	}

	public function index()
	{

		$this->WeChat = new WeChatConsole();

		try {
			$this->Receive = $this->WeChat->load('Receive');
		} catch (\Exception $e) {
			echo $e->getMessage();
			exit();
		}

		$this->data = $this->Receive->getReceive();

		$this->openid = $this->Receive->getOpenid();

	}

}

