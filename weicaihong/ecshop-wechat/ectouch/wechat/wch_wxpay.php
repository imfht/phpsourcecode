<?php

/**
 * wch_wxpay.php  UTF8
 * log_id正确的情况下才会通知,总共通知8次
 * @author weicaihong 微彩虹--微商城 Ecshop微商城深度开发
 * @date 2015-3-1
 * @copyright http://www.weicaihong.com
 */

// 订单处理
include_once(ROOT_PATH . 'includes/lib_payment.php');

$log_id = $post_data['log_id'];

if($log_id)
{
    // 改变订单状态
    order_paid($log_id, 2);
    $data = array('msg'=>$log_id);
}

// 输出json
require_once('wch_json.php');