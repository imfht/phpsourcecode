<?php

$config = array();

$config['product_name'] = 'Micro Service Api';
$config['product_release_ver'] = '1.0.0 Build 20150925';

$config['cookiePre'] = 'msapi_';
$config['hashSalt'] = '64rnu-l9032qumgyDTY,B908)(^yyyyyyaksdathd';

/**
 * 允许使用本微服务的ip。默认为本地ip数组。
 * 如果要让所有ip均可访问，请将其赋值为*，即：
 *     $config['microserviceapi_allow_ip'] = "*";
 * 注意：本设置为全局设置，只有通过了本设置，才进入各应用自己的allow_ip配置中
 */
$config['microserviceapi_allow_ip'] = array(
    '127.0.0.1',
    '0.0.0.0',
);

$config['microserviceapi_request_validate_class'] = 'Apphook\MicroServiceApiRequestValidate_BasicAuth';

$config['microserviceapi_auth_timediff_max'] = 60;
$config['microserviceapi_version'] = '1.0';

$config['client_app_1'] = array(
    'appid' => 1,
    'appsecret' => '11111112222233333333',
    //allow_ip可以不设置，不设置时，等同于如下效果：仅本地ip数组可使用。
    /*
    'allow_ip' => array(
        '127.0.0.1',
        '0.0.0.0',
    ),
    */
);

return $config;