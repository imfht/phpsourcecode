<?php
/**
 * 钱和积分的表单验证器
 */
namespace app\system\validate;
use think\Validate;

class UserBank extends Validate{

    protected $rule = [
        'user_id'        => 'require|integer',
        'safepassword'   => 'require',
        'name'           => 'require',
        'bankname'       => 'require',
        'bankid'         => 'require|min:10',
        'bankid_confirm' => 'require|confirm:bankid',
        'idcard'         => 'require|idCard',
    ];

    protected $message = [
        'user_id'          => '未找到对应用户',
        'safepassword'     => '安全验证密码没有输入',
        'name'             => '姓名必须填写',
        'bankname'         => '开户行必须填写',
        'bankid'           => '银行卡号必须填写',
        'bankid_confirm'   => '两次输入卡号不一致',
        'idcard'           => '身份证输入错误',
    ];

    protected $scene = [
        'bind' => ['name','bankname','bankid','idcard','bankid_confirm','safepassword']  //绑定银行账号信息
    ];
}