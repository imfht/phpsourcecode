<?php
/**
 * types 应用类型 参数：mp、mp_program_app、mp_program、app、program
 * version    应用版本好
 * name 应用名称
 * describe 应用描述
 * is_openapp 开发平台应用  0平台、1独立
 * is_manage  启用总平台管理  0关闭1启用
 * is_wechat_pay 微信支付  0、1
 * is_alipay_pay 支付宝支付  0、1
 */
return [
    'types'         => 'program',  //mp、mp_program_app、mp_program、app、program
    'version'       => '1.1.0',
    'name'          => '闪客多店',
    'describe'      => "模式商城,针对有团队的特定群体,把产品组合抢购,一次抢购多个产品组合套餐,买一赠二、提一卖二、成交赚钱",
    'is_openapp'    => 0,
    'is_manage'     => 0,
    'is_wechat_pay' => 1,
    'is_alipay_pay' => 0,
];
