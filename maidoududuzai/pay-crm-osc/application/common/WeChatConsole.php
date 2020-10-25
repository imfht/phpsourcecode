<?php

namespace app\common;

use \think\Db;

class WeChatConsole extends \app\common\WeChat
{

	public function __construct($Method = null, $config = [])
	{
		parent::__construct($Method, $config);
	}

	public static function init($config = [])
	{
		// sys cfg
		$self = new self();
		if(empty($config['cache_path'])) {
			$config['cache_path'] = TEMP_PATH . 'WeChat';
		}
		$self->set($config);
		return $self;
	}

}

