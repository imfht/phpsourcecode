<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/20
 * Time: 11:08
 */
return [
    'api_auth' => true,  //是否开启授权认证
    //'auth_class'=> app\first\auth\OauthAuth::class,
    'auth_class'=> app\first\auth\BasicAuth::class,
    'api_debug'=> false,//是否开启调试
    'passphrase'=> 'jswei30',
    'api_version'=> 'v1',
    'version' => '1.0',
    'domain'=> 'http://www.tp5.com'
];