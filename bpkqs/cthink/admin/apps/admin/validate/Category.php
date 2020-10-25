<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 分类验证器
 * @auth zhanghd <zhanghd1987@foxmail.com>
 */
class Category extends Validate
{	
    protected $rule = [
        'title'  =>  'require|unique:category',
    ];

    protected $message = [
        'title.require' => '分类名称不能为空',
		'title.unique'	=> '分类名称已经存在',
	];

    protected $scene = [
        'add'   =>  ['title'],
		'edit'	=>	['title'],
    ];
}
