<?php
namespace app\ucenter\validate;

use think\Validate;
use think\Db;

class UcenterMember extends Validate
{
    //需要验证的键值
    protected $rule =   [
        'sku_id' => 'regex:/^([0-9]+)/|unique',
        'quantity' => 'regex:/^[1-9]\d*$/'
    ];

    //验证不符返回msg
    protected $message  =   [
        'sku_id.regex'               => 'sku_id格式错误',
        'sku_id.unique'              => '你已经添加过了',
        'quantity.regex'             => '数量错误',
    ];
    //验证场景
    protected $scene = [
        //'password'  =>  ['password','confirm_password'],
        //'reg'  =>  ['username','email','password'],
    ];

    // 自定义验证规则

 }   