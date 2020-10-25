<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\http\validate;

use think\Validate;

/**
 * 菜单验证
 */
class Ad extends Validate{
	protected $rule = [
		'title'  =>  'require',
		'name'  =>  'require|unique:AdPlace',
	];

	protected $message  =   [
		'title.require' => '广告位标题必须',
		'name.require' => '广告位标识必须',
		'name.unique' => '广告位标识已存在',
	];

	protected $scene = [
		'adminadd'  =>  ['title', 'name'],
		'adminedit'  =>  ['title', 'name'],
	]; 
}