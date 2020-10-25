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
class Config extends Validate{
	protected $rule = [
		'name'  =>  'require|unique:config',
		'title'  =>  'require',
	];

	protected $message  =   [
		'name.require' => '配置标识必须',
		'name.unique' => '配置标识已存在',
		'title.require' => '配置标题必须',
	];

	protected $scene = [
		'adminadd'  =>  ['title', 'name'],
	];

	// edit 验证场景定义
	public function sceneAdminedit() {
		return $this->only([['title', 'name']])
			->remove('name', 'unique');
	} 
}