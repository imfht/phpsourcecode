<?php
return array(
	'URL_ROUTER_ON' => true, 
	'URL_ROUTE_RULES'=>array(    
		
	),

	//支付宝配置相关
	'alipay_config' => array(
		'partner' => '123',
		'key' => '123',
		'sign_type' => 'MD5',
		'input_charset' => 'utf-8',
		'transport' => 'http',
		'cacert' => getcwd().'\\cacert.pem',
	),
	'seller_email' => 'XXXX@qq.com',
	'format' => 'xml', //返回格式
	'v' => "2.0", //版本号
	'req_id' => date('YmdHis'), //请求ID
	'notify_url' => "http://www.xxx.com/tpstudy/dayima/index.php/Pay/notify_url.html",
	'call_back_url' => "http://127.0.0.1/tpstudy/dayima/index.php/Pay/return_url.html",
	'merchant_url' => "http://127.0.0.1:8800/WS_WAP_PAYWAP-PHP-UTF-8/xxxx.php",
);