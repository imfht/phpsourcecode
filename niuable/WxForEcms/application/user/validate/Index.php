<?php
namespace app\reply\validate;

use think\Validate;

class Index extends Validate
{
	protected $rule = [
			
	];
	protected $message = [
		
	];

	public function __construct()
	{
		// 开启批量验证
		$this->batch();
	}

}