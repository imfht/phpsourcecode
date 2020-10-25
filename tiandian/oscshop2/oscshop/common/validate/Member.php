<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 * 用户注册验证
 */ 
namespace osc\common\validate;
use think\Validate;
class Member extends Validate
{
    protected $rule = [
        'username'  =>  'require|min:2|unique:member',
        'password'=>'require|min:6',
        'pwd2'=>'require|confirm:password'    
    ];

    protected $message = [
        'username.require'  =>  '用户名必填',
        'username.min'  =>  '用户名不能小于两个字',     
        'username.unique'  =>  '用户名已经存在',
        
		'password.require'  =>  '密码必填',
		'password.min'  =>  '密码不能小于6位',  
		'pwd2.require'  =>  '确认密码必填',
		'pwd2.confirm'  =>  '两次密码不一样'
		
    ];
	
}
?>