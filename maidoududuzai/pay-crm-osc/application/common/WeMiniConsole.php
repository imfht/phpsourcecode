<?php

namespace app\common;

use \think\Db;

Class WeMiniConsole extends \app\common\WeMini
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
			$config['cache_path'] = TEMP_PATH . 'WeMini';
		}
		$self->set($config);
		return $self;
	}

}

