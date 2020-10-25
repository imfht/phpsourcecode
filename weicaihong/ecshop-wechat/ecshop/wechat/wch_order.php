<?php
/**
 * wch_order.php UTF8
 * 路径 /wechat/wch_order.php
 * User: weicaihong.com
 * Date: 15-06-09 17:14
 * Copyright: http://www.weicaihong.com
 * 使用方法
 * /admin/order.php 900行处
 * require(ROOT_PATH.'wechat/wch_order.php'); // 微彩虹发货通知
 * if ($GLOBALS['_CFG']['sms_order_shipped'] == '1' && $order['mobile'] != '') {...}  之后添加
 *
 */

// 微彩虹发货通知
require_once(ROOT_PATH.'wechat/wch_lib.php');
$go_url = 'http://mp.weicaihong.com/index.php/open/ecshop/tongzhi'.'?wchToken='.md5(appId);
$query_sql = "SELECT wxid FROM " . $ecs->table('users') . " WHERE user_id = '$order[user_id]'";
$ret_w = $db->getRow($query_sql);
$wxid = $ret_w['wxid'];
$wch_data = array(
	'wxid'=>$wxid, 
	'act'=>'order',
    'url'=>'', //订单的快递详情或者是快递信息
	'keyword1'=>$order['order_sn'],   // 订单编号
	'keyword2'=>$order['shipping_name'], // 物流公司
	'keyword3'=>$order['invoice_no'] // 物流单号
);
$wch_json = wch_curl_post($go_url,$wch_data);
$wch_res = json_decode($wch_json);
if($wch_res->errmsg == 'ok')
{
    // 通知成功  $wch_res->msg
}
elseif($wch_res->errmsg == 'error')
{
    // 通知失败 $wch_res->msg
}
