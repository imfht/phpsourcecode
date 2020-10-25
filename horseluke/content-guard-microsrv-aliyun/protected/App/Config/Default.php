<?php

$config = array();

$config['product_name'] = '阿里云安全API演示';
$config['product_release_ver'] = '1.0.1 Build 20150926';

$config['cookiePre'] = 'demoit_';
$config['hashSalt'] = 'asdatyi03myhyiawfu';

$config['hooks'] = array();
$config['hooks']['controller_init'] = array('Apphook\Init'. D_CONTROLLER_NAME, 'run');

$config['ALIBABASDK_SERVICELOCATOR_CONFIG_FILENAME'] = 'ConfigServiceLocatorDefault';

$config['TAOBAO_APPKEY'] = '';
$config['TAOBAO_APPSECRET'] = '';

$config['ALIYUN_ACCESSKEY_ID'] = '';
$config['ALIYUN_ACCESSKEY_SECRET'] = '';

$config['ALIBABASDK_FILE_LOG_DIR'] = '';

$config['MICROSRV_GATEWAYURL'] = '';
$config['MICROSRV_APPID'] = '';
$config['MICROSRV_APPSECRET'] = '';

$config['MICROSRV_FILE_LOG_DIR'] = '';

return $config;