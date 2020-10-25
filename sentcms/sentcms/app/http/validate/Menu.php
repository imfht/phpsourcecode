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
class Menu extends Validate{
	protected $rule = [
		'title'  =>  'require',
	];

	protected $message  =   [
		'title.require' => '菜单名称必须',
	];

	protected $scene = [
		'adminadd'  =>  ['title'],
		'adminedit'  =>  ['title'],
	]; 
}