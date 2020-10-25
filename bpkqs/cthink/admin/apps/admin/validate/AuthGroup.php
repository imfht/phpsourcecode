<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 创建角色组验证器
 * @auth zhanghd <zhanghd1987@foxmail.com>
 */
class AuthGroup extends Validate
{	
    protected $rule = [
        'title'  =>  'require|max:16|unique:auth_group',
		'info'	=>	'max:50',
    ];

    protected $message = [
        'title.require' => '角色不能为空',
		'title.max'		=> '角色长度不能大于16位',
		'title.unique'	=> '角色名已经存在',
		'info.max'		=> '角色描述长度不可以超过50个字符'
	];

    protected $scene = [
        'add'   =>  ['title','info'],
		'edit'	=>	['title','info'],
    ];
}
