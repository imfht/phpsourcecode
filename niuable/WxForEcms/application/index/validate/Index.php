<?php
namespace app\index\validate;

use think\Validate;

class Index extends Validate
{
	protected $rule = [
			'name|名称'  =>  'require|unique:wx_wx',
			'type|类型' =>  'require',
			'way_of_key|加密方式' =>  'require',
			'app_id|AppID' =>  'require',
			'app_secret|AppSecret' =>  'require',
			'token|Token口令' =>  'require|min:3',
			'encoding_aes_key|EncodingAesKey' => 'requireWith:way_of_key',
	];
	protected $message = [
		'name.unique' => '该名称已存在',
		'encoding_aes_key'	=>	'兼容或加密模式下，该项必须填写',
	];

	public function __construct()
	{
		// 开启批量验证
		$this->batch();
	}

}