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
class Link extends Validate{
	protected $rule = [
		'title'  =>  'require',
		'url'    => 'require|url'
	];

	protected $message  =   [
		'title.require' => '连接标题必须',
		'url.require' => '连接地址必须',
		'url.url' => '连接格式错误',
	];

	protected $scene = [
		'adminadd'  =>  ['title', 'url'],
		'adminedit'  =>  ['title', 'url'],
	]; 
}