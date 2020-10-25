<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 2:31 PM
 */

/**
 * 错误编码0、1为保留编码，0为成功，1为需要持续提示信息
 */

return [
    //公共错误提示
    'invalid_request' => '10000|无效的请求',
    'missing_params' => '10001|缺少参数',
    'invalid_params' => '10002|无效的参数',
    'invalid_token' => '10003|无效的token',
    'invalid_sign' => '10004|无效的签名',
    'invalid_device' => '10005|无效的设备',
    'timestamp_error' => '10006|无效的时间戳',
    'timestamp_out' => '10007|时间超时',
    'fail' => '10008|操作失败',
    'content_is_empty' => '10009|内容为空',
    'captcha_error' => '10010|验证码错误',
    'role_error' => '10011|权限不足',
    'user_freeze' => '10012|用户已经被冻结',
    'user_blacklist' => '10013|用户已经被加入黑名单',
    'save_error' => '10014|保存失败',
    'del_error' => '10015|删除失败',
    'admin_role_no_del' => '10016|超级权限禁止删除',
    'index_right_no_del' => '10017|主页权限禁止删除',
    'default_seller_no_del' => '10018|默认商家禁止删除',
    'upload_model' => '10019|上传类型错误',


    'admin_user_error' => '10030|用户名错误',
    'admin_password_error' => '10031|密码错误',
    'admin_password_empty' => '10032|密码不能为空',
    'admin_in_blacklist' => '10033|用户被锁定，请联系管理员',
    'category_child_no_empty' => '10034|该分类存在下级分类，不能删除',
    'menu_child_no_empty' => '10035|该菜单存在下级分类，不能删除',

    //订单
    'order_error' => '20001|订单错误',
    'order_status_error' => '20002|订单状态错误',
    'order_delivery_price_real_error' => '20003|订单运费金额不能小于0元',
    'order_goods_error' => '20004|订单商品不存在',
    'refund_status_error' => '20005|售后状态错误',
    'refund_address_error' => '20006|退货地址不存在',
    'refund_amount_fail' => '20007|打款失败，请查看具体错误',
    'express_company_error' => '20008|物流公司信息错误',
    'delivery_address_error' => '20009|发货地址不存在',

    //资金
    'balance_event_error' => '30001|资金类型错误',
    'balance_insufficient' => '30002|余额不足',

    //优惠券
    'coupons_not_exists' => '40000|优惠券不存在',
    'coupons_overdue' => '40001|优惠券已经过期',
    'coupons_status_error' => '40002|优惠券状态错误',
    'coupons_pct_error' => '40003|折扣值只能是1-100的整数',
    'coupons_max_500' => '40004|一次最多只能生成500张',
    'bind_user_error' => '40005|用户不存在',

    //促销活动
    'promotion_end' => '40020|优惠活动已经过期',
    'promotion_pct_error' => '40021|活动折扣值只能是1-100的整数',
    'promotion_point_error' => '40022|赠送积分必须大于等于0',
    'promotion_coupons_id_error' => '40023|优惠券id不能为空',

    //商品
    'goods_shelves_status_fail' => '50001|操作失败，注意商品状态必须是已审核'
];