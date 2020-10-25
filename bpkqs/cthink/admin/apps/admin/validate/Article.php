<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 文章管理的验证器
 * @author zhanghd <zhanghd1987@foxmail.com>
 */
class Article extends Validate
{
	protected $rule = [
        'title'  =>  'require|max:100|unique:article',
		'info'	=>	'max:50',
    ];

    protected $message = [
        'title.require' => '文章标题不能为空',
		'title.max'		=> '最大可以输入100个字符',
		'title.unique'	=> '角色名已经存在',
	];

    protected $scene = [
        'add'   =>  ['title'],
		'edit'	=>	['title'],
    ];
}
