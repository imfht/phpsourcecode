<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.sycit.cn
// +----------------------------------------------------------------------
// | Author: Peter.Zhang  <hyzwd@outlook.com>
// +----------------------------------------------------------------------
// | Date:   2017/8/19
// +----------------------------------------------------------------------
// | Title:  Users.php
// +----------------------------------------------------------------------
 namespace app\index\validate;

 use think\Validate;

 class Users extends Validate
 {
     protected $rule = [
         'username|登录账户' => 'require|alphaDash|length:1,25',
         'password|登录密码' => 'require|alphaDash|length:6,30',
         'verify|验证码'     => 'require|check_verify:sycitcn',
         'nickname|员工姓名' => 'require|chs',
         'bumen|部门'        => 'require',
         '__token__|数据'    =>  'require|token'
     ];
     protected $message = [
         'username.require'   => ':attribute不能为空',
         'username.alphaDash' => ':attribute只能使用字母和数字下划线_及破折号-',
         'username.length'    => ':attribute长度范围6-25',
         'password.require'   => ':attribute不能为空',
         'password.alphaDash' => ':attribute只能使用字母和数字下划线_及破折号-',
         'password.length'    => ':attribute长度范围6-30',
         'verify.require'     => ':attribute不能为空',
         'verify.check_verify:sycitcn' => ':attribute 错误',
         'bumen.require'      => ':attribute不能为空',
         'nickname.require'   => ':attribute不能为空',
         'nickname.chs'       => ':attribute只能中文',
         '__token__.require'  => ':attribute不能为空',
     ];
     protected $scene = [
         'register' => ['username', 'password', '__token__'],
         'login'    => ['username', 'password', '__token__'],
         'add'      => ['username', 'nickname', 'password', 'bumen', '__token__'],
         'edit'     => ['username', 'nickname', 'bumen', '__token__'],
         'update'   => ['password']
     ];

     // 自定义规则
     public function check_verify($value,$rule)
     {
         //return true;
         $verify = new \org\Verify();
         return $rule == $verify->check($value) ? true : false;
     }
 }