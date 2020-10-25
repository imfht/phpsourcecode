<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:31 PM
 */

return [
    //公共错误提示
    'invalid_request' => '10000|无效的请求',
    'missing_params' => '10001|缺少参数',
    'invalid_params' => '10002|无效的参数',
    'invalid_token' => '10003|无效的token',
    'invalid_sign' => '10004|无效的签名',
    'invalid_device' => '10005|无效的设备',
    'invalid_platform' => '10006|无效的来源',
    'timestamp_error' => '10007|无效的时间戳',
    'timestamp_out' => '10008|时间超时',
    'fail' => '10009|操作失败',
    'content_is_empty' => '10010|内容为空',
    'role_error' => '10011|权限不足',
    'upload_model' => '10012|上传类型错误',
    'captcha_type' => '10013|验证码类型错误',
    'mobile_format' => '10014|手机号码格式错误',
    'sms_captcha_error' => '10015|手机验证码错误',
    'sms_captcha_time_out' => '10016|手机验证已经过期',
    'sms_frequent' => '10017|短信发送太频繁',
    'sms_send_fail' => '10018|短信发送失败',
    'seller_error' => '10019|商家信息错误',
    'address_error' => '10020|地址信息错误',
    'goods_search_max_page' => '10021|商品搜索分页最大100页',

    //用户
    'user_error' => '20000|用户不存在',
    'user_is_login' => '20001|用户已经登录',
    'user_no_login' => '20002|用户未登录',
    'password_error' => '20003|用户名或密码错误',
    'old_password_error' => '20004|用户原密码错误',
    'old_pay_password_error' => '20005|支付密码错误',
    'pay_password_notset' => '20006|支付密码未设置',
    'pay_password_isset' => '20007|已经设置过支付密码',
    'pay_password_error' => '20008|支付密码错误',
    'user_is_repeat' => '20009|用户已经存在',
    'user_freeze' => '20010|用户已经被冻结',
    'user_blacklist' => '20011|用户已经被加入黑名单',
    'user_mobile_error' => '20012|用户手机号码错误',
    'auth_type_error' => '20013|第三方类型错误',
    'auth_data_error' => '20014|第三方数据错误',
    'user_mobile_is_bind' => '20015|手机号已经绑定其他账号',

    //购物车商品
    'tip_goods_no_shelves' => '商品已下架',//购物车错误提示不需要编码
    'tip_goods_stock_no_enough' => '库存不足',//购物车错误提示不需要编码
    'tip_goods_min_buy_qty_error' => '最少需要订购',//购物车错误提示不需要编码
    'delivery_can_not' => '不在配送范围内',//购物车错误提示不需要编码
    'goods_error' => '30001|商品不存在',
    'goods_shelves_status_error' => '30002|商品已失效',
    'goods_sku_error' => '30003|商品不存在',
    'goods_sku_status_error' => '30004|商品已失效',
    'goods_min_buy_qty_error' => '30005|商品最少需要订购',
    'goods_stock_no_enough' => '30006|商品库存不足',
    'cart_goods_error' => '30007|购物车商品不存在',
    'search_key_and_category_error' => '30008|关键字和分类不能都为空',

    //资金
    'balance_event_error' => '40001|资金类型错误',
    'balance_insufficient' => '40002|余额不足',
    'recharge_error' => '40003|充值单信息错误',
    'recharge_status_error' => '40003|充值单状态错误',
    'recharge_not_balance_pay' => '40004|余额充值不支持余额支付',

    //积分
    'point_event_error' => '50001|资金类型错误',
    'point_insufficient' => '50002|余额不足',

    //优惠券
    'coupons_not_exists' => '60001|优惠券不存在',
    'coupons_overdue' => '60002|优惠券已经过期',
    'coupons_is_use' => '60003|优惠券已使用',
    'coupons_obtain_max' => '60004|已经领取过该优惠券',//超过数量
    'coupons_no_use' => '60004|优惠券不可使用',

    //订单
    'order_error' => '70001|订单不存在',
    'order_status_error' => '70002|订单状态错误',
    'order_pay_status_error' => '70003|订单已经支付或取消',
    'order_delivery_price_real_error' => '70004|订单运费金额不能小于0元',
    'order_goods_error' => '70005|订单商品不存在',
    'order_submit_fail' => '70006|订单提交失败',
    'trade_submit_fail' => '70007|交易单创建失败，请进入我们的订单查看',
    'goods_is_update' => '70008|商品信息已经发生变化，请重新提交',
    'delivery_error' => '70009|配送方式错误，请重新选择',
    'cart_goods_not_exists' => '20010|购物车商品不存在',
    'buy_qty_error' => '70011|商品购买数量错误',
    'address_not_exists' => '70012|收货地址不存在',
    'goods_can_not_delivery' => '70013|存在不可配送的商品',
    'payment_error' => '70014|支付方式不存在',
    'trade_create_fail' => '70015|交易单创建失败',
    'pay_openid_error' => '70016|用户openid错误',
    'evaluation_level_error' => '70017|评价等级只能是1-5',
    'refund_error' => '70018|售后信息错误',
    'refund_status_error' => '70018|售后状态错误',
    'refund_complete' => '70018|售后已经完成',
    'refund_time_out' => '70019|商品已经过了售后时间，请联系客服',
    'refund_replace_complete' => '70020|已经换货的不能再次申请，请联系客服',
    'refund_wait_audit' => '70021|售后处理中',
    'refund_amount_error' => '70022|售后金额不能大于最大金额',
    'express_company_error' => '70023|物流公司信息错误',
    'invoice_title_error' => '70024|发票抬头不能为空',
    'invoice_tax_no_error' => '70025|纳税人识别号不能为空',
    'cart_contains_special_goods' => '70026|购物车包含秒杀、拼团等特殊商品',
];
