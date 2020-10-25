<?php
/**
 * types 应用类型 参数：mp、mp_program_app、mp_program、app、program
 * is_openapp 开发平台应用  0、1
 * is_manage  启用总平台管理  0、1
 * version    应用版本好  0、1
 * is_wechat_pay 微信支付  0、1
 * is_alipay_pay 支付宝支付  0、1
 * name 应用名称
 * describe 应用描述
 */
return [
    'types'         => 'program',  //mp、mp_program_app、mp_program、app、program
    'version'       => '1.0.0',
    'name'          => '小绿叶',
    'describe'      => "绿色回收.",
    'is_openapp'    => 0,
    'is_manage'     => 1,
    'is_wechat_pay' => 1,
    'is_alipay_pay' => 0
];
