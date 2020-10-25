<?php
namespace app\file\validate;

use think\Validate;

class Index extends Validate
{
	protected $rule = [
		'title|标题'=>'require',
	];
	protected $message = [
		
	];

	public function __construct()
	{
		// 开启批量验证
		$this->batch();
	}

}