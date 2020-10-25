<?php
return array(
//***********************************URL设置**************************************
    'MODULE_ALLOW_LIST'      => array('Home','Api'), //允许访问列表
    'URL_HTML_SUFFIX'        => '',  // URL伪静态后缀设置
    'URL_MODEL'              => 2,  //启用rewrite
	
//***********************************微信支付**********************************
	'WEIXINPAY_CONFIG'       => array(
		'APPID'              => 'wx452cfdae72fbb4ff', // 微信支付APPID
        'MCHID'              => '1351194001', // 微信支付MCHID 商户收款账号
        'KEY'                => '66cf4e688c3518406c636f340c33e271', // 微信支付KEY
        'APPSECRET'          => '66cf4e688c3518406c636f340c33e272',  //公众帐号secert
        'NOTIFY_URL'         => 'http://baicheng.dakaifa.net/Pay/WeixPay/notify/order_number/', // 接收支付状态的连接，这暂时没写
	),
);