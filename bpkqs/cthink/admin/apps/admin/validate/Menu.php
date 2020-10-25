<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 创建节点菜单验证器
 * @auth zhanghd <zhanghd1987@foxmail.com>
 */
class Menu extends Validate
{	
    protected $rule = [
        'title'  =>  'require|max:16|unique:menu',
    ];

    protected $message = [
        'title.require' => '菜单名称不能为空',
		'title.max'		=> '菜单名称长度不能大于16位',
		'title.unique'	=> '菜单名称已经存在',
	];

    protected $scene = [
        'add'   =>  ['title'],
		'edit'	=>	['title'],
    ];
}
