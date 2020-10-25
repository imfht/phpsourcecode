<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/7 0007
 * Time: 09:29
 */
namespace app\weixin\validate;


use think\Validate;

class CodeValidate extends Validate
{

    protected $rule=[
        'phone'  => ['require', 'min' => 11, 'regex' => '/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$|17[0-9]{9}$/'],
        'code'=>['require']


    ];
    protected $message  =   [
        'phone.require' => '手机号码不能为空',
        'phone.min'=>'手机号码至少11位',
        'phone.regex'=>'手机号码格式错误',
        'code.require'=>'验证码不能为空'
    ];

    protected $scene = [
        'login'  =>  ['phone','age'],
        'codes'=>['phone']
    ];

//    protected $scenes = [
//        'login'  =>  ['phone','age'],
//    ];

}