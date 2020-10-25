<?php
$spname = mc_option('site_name').'订单';
$partner = mc_option('tenpay_seller');                                  	//财付通商户号
$key = mc_option('tenpay_key');											//财付通密钥

$return_url = mc_option('site_url').'/pay/tenpay_return_url.php';			//显示支付结果页面,*替换成payReturnUrl.php所在路径
$notify_url = mc_option('site_url').'/pay/tenpay_notify_url.php';			//支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径
?>