<?php
// 权限验证       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\validate;

use think\Validate;

class AuthGroupValidate extends Validate
{

	protected $rule = [
	    'title|权限组名'=>'require|regex:/[\x7f-\xff]/',
        'status'=>'require|number|in:0,1',
        'description'=>'max:255',
        'rules'=>'require|array'
	];

	protected $message = [
	    'title.regex'=>'权限组名必须为中文',
        'status'=>'权限状态错误,请重新选择',
        'description'=>'权限描述不得大于255字节'
    ];
}