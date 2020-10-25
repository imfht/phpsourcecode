<?php
/**
 * 钱和积分的表单验证器
 */
namespace app\green\validate;
use think\Validate;

class Bank extends Validate{

    protected $rule = [
        'id'             => 'require|integer',
        'user_id'        => 'require|integer',
        'money'          => 'require|moneys|gt: 0',
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
    ];

    protected $message = [
        'id'               => '配置ID丢失',
        'user_id'          => '未找到对应用户',
        'money.require'    => '金额必须填写',
        'money.moneys'     => '金额输入错误,禁止大于10万',
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
    ];

    protected $scene = [
        'cash'     => ['id','ispass','realmoney'],        //后台提现审核
        'getcash'  => ['user_id','money','safepassword'], //API提现申请
        'bankInfo' => ['name','bankname','bankid','idcard','bankid_confirm','safepassword'] //提交提现银行信息
    ];

    //人民币验证
    protected function moneys($value){
        $rule = '/^(0|[1-9]\d{0,4})(\.\d{1,2})?$/';
        $rel = preg_match($rule, $value);
        return $rel ? true : false;
    }
}