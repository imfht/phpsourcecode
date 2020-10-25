<?php
/**
 * types 应用类型 参数：mp、mp_program_app、mp_program、app、program
 * is_openapp 开发平台应用  0、1
 * is_diyapp 是否专属应用  0、1
 * is_manage  启用总平台管理  0、1
 * version    应用版本好  0、1
 * is_wechat_pay 微信支付  0、1
 * is_alipay_pay 支付宝支付  0、1
 * 
 * name 应用名称
 * describe 应用描述
 */
return [
    'types'         => 'mp_program_app',  //mp、mp_program_app、mp_program、app、program
    'version'       => '1.8.6',
    'name'          => 'SAPI++管理中心',
    'describe'      => "一款免费开源的公众号，微信小程序，支付宝小程序，PC建站为一体的SaaS服务管理系统",
    'is_openapp'    => 0,
    'is_diyapp'     => 0,
    'is_manage'     => 0,
    'is_wechat_pay' => 0,
    'is_alipay_pay' => 0
];
