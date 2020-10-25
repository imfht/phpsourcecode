<?php
/**
 * 钱和积分的表单验证器
 */
namespace app\popupshop\validate;
use think\Validate;

class Bank extends Validate{

    protected $rule = [
        'id'             => 'require|integer',
        'user_id'        => 'require|integer',
        'money'          => 'require|float|gt: 0',
        'realmoney'      => 'require|float',
        'safepassword'   => 'require',
        'phone_id'       => 'require|mobile',
        'sms_code'       => 'require|min:4|max:6',
        'ispass'         => 'require|integer|egt:0|elt:1',
        'name'           => 'require',
        'bankname'       => 'require',
        'bankid'         => 'require|min:10',
        'bankid_confirm' => 'require|confirm:bankid',
        'idcard'         => 'require|idCard',
        'shop_money'     => 'require|float|>=:0',
        'due_money'      => 'require|float|>=:0',
    ];

    protected $message = [
        'id'               => '配置ID丢失',
        'user_id'          => '未找到对应用户',
        'money.require'    => '金额必须填写',
        'money.float'      => '金额输入不正确',
        'money.gt'         => '金额必须大于0',
        'realmoney'        => '实际到账必须填写',
        'safepassword'     => '安全验证密码没有输入',
        'phone_id.require' => '手机号必须填写',
        'phone_id.mobile'  => '手机号输入格式错误',
        'sms_code'         => '验证码填写不正确',
        'ispass'           => '是否通过必须选择',
        'name'             => '姓名必须填写',
        'bankname'         => '开户行必须填写',
        'bankid'           => '银行卡号必须填写',
        'bankid_confirm'   => '两次输入卡号不一致',
        'idcard'           => '身份证输入错误',
        'shop_money'       => '应付积分不能小于 0',
        'due_money'        => '购物积分不能小于 0',
    ];

    protected $scene = [
        'cash'     => ['id','ispass','realmoney'], //后台提现审核
        'getcash'  => ['user_id','money','safepassword'], //API提现申请
        'bankInfo' => ['name','bankname','bankid','idcard','bankid_confirm','safepassword'], //提交提现银行信息
        'recharge' => ['user_id','safepassword','shop_money','due_money'], //充提
        'transfer' => ['money','sms_code','phone_id','safepassword'] //转账
    ];
}