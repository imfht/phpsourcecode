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
class Category extends Validate{
	protected $rule = [
		'title'  =>  'require',
		'model_id' => 'require'
	];

	protected $message  =   [
		'title.require' => '栏目名称必须',
		'model_id.require' => '绑定模型必须',
	];

	protected $scene = [
		'adminadd'  =>  ['title', 'model_id'],
		'adminedit'  =>  ['title', 'model_id'],
	]; 
}