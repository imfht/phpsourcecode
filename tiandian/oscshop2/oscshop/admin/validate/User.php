<?php

namespace osc\admin\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'user_name'  =>  'require|min:2|unique:admin',  
        'passwd'   =>'require'
    ];

    protected $message = [
        'user_name.require'  =>  '用户名必填',
        'user_name.min'  =>  '用户名不能小于两个字',  
        'user_name.unique'  =>  '用户名已经存在',     
        'passwd.require'  =>  '密码必填',
    ];
	
	 protected $scene = [
        'edit'  =>  ['user_name'=>'require|min:2'],
    ];
	
}
?>