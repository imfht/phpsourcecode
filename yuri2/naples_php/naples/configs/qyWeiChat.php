<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/12/8
 * Time: 15:38
 */

//企业微信配置
return [
//    'configuration'=>'default',
    'default'=>[
        'token' => 'xxxxx', //填写应用接口的Token
        'encodingaeskey' => 'xxxxxxxxxxx', //填写加密用的EncodingAESKey
        'appid' => 'xxxxxxxxxx', //填写高级调用功能的app id
        'appsecret' => 'xxxxxxxxx', //填写高级调用功能的密钥
        'agentid'=>'xxxxx', //应用的id
        'debug'=>false, //调试开关
        'logcallback'=>'fastLog', //调试输出方法，需要有一个string类型的参数
    ],
];