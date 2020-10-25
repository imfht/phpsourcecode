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
class Model extends Validate{
	protected $rule = [
		'title'  =>  'require',
		'name'  =>  'require|unique:Model|alpha',
	];

	protected $message  =   [
		'title.require' => '模型名称必须',
		'name.require' => '模型标识必须',
		'name.unique' => '模型标识已存在',
		'name.alpha' => '模型标识必须为字母',
	];

	protected $scene = [
		'adminadd'  =>  ['title', 'name'],
		'adminedit'  =>  ['title'],
	]; 
}