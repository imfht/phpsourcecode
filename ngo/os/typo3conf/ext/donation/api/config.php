<?php
//从插件获取配置信息
$localConfig = include('../../../../typo3conf/LocalConfiguration.php');
$alipayConfig = unserialize($localConfig['EXT']['extConf']['payment']);

//=======【支付宝基本信息设置】=====================================
$GLOBALS['ALIPAY_ACCOUNT'] = $alipayConfig['alipay_account']?$alipayConfig['alipay_account']:$localConfig['ALIPAY']['account']; //支付宝账号
$GLOBALS['ALIPAY_PARTNER'] = $alipayConfig['alipay_partner']?$alipayConfig['alipay_partner']:$localConfig['ALIPAY']['partner']; //合作者身份
$GLOBALS['ALIPAY_KEY'] = $alipayConfig['alipay_key']?$alipayConfig['alipay_key']:$localConfig['ALIPAY']['key']; //安全校验码


//=======【微信基本信息设置】=====================================
//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
$GLOBALS['WX_APPID'] = $alipayConfig['wx_appid']?$alipayConfig['wx_appid']:$localConfig['WECHAT']['appid'];
//受理商ID，身份标识
$GLOBALS['WX_MCHID'] = $alipayConfig['wx_mchid']?$alipayConfig['wx_mchid']:$localConfig['WECHAT']['mchid'];
//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
$GLOBALS['WX_KEY'] = $alipayConfig['wx_key']?$alipayConfig['wx_key']:$localConfig['WECHAT']['key'];
//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
$GLOBALS['WX_APPSECRET'] = $alipayConfig['wx_appsecret']?$alipayConfig['wx_appsecret']:$localConfig['WECHAT']['appsecret'];

//=======【curl超时设置】===================================
//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
$GLOBALS['WX_CURL_TIMEOUT'] = 60;

//=======【证书路径设置】=====================================
//证书路径,注意应该填写绝对路径
$GLOBALS['WX_SSLCERT_PATH'] = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_cert.pem';
$GLOBALS['WX_SSLKEY_PATH'] = '/xxx/xxx/xxxx/WxPayPubHelper/cacert/apiclient_key.pem';