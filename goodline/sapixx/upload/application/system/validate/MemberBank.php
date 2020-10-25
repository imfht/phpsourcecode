<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 钱和积分的表单验证器
 */
namespace app\system\validate;
use think\Validate;

class MemberBank extends Validate{

    protected $rule = [
        'id'           => 'require|integer',
        'miniapp_id'   => 'require|integer',
        'member_id'    => 'require|integer',
        'money'        => 'require|integer|egt:10',
        'safepassword' => 'require',
        //充值
        'payType'      => 'require|integer',
    ];

    protected $message = [
        'id'                 => '配置ID丢失',
        'member_id'          => '未找到对应用户',
        'money.require'      => '充值金额必须填写',
        'money.integer'      => '充值金额必须是整数',
        'money.egt'           => '充值金额必须大于10元',
        'safepassword'       => '安全验证密码没有输入',
    ];

    protected $scene = [
        'buy'      => ['id','title','member_id','safepassword'], //购买应用
        'recharge' => ['payType','money'],    //充值
    ];
}