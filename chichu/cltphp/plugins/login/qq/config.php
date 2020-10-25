<?php
return array(
    'code'=> 'qq',
    'name' => 'QQ登陆',
    'version' => '1.0',
    'author' => 'CLTPHP',
    'desc' => 'QQ登陆插件 ',
    'icon' => 'logo.png',
    'config' => array(
        array('name' => 'APP_KEY','label'=>'appid','type' => 'text',   'value' => ''),
        array('name' => 'APP_SECRET','label'=>'appkey','type' => 'text',   'value' => ''),
        array('name' => 'CALLBACK','label'=>'回调地址','type' => 'text',   'value' => 'http://'.$_SERVER['HTTP_HOST'].'/user/callback/qq?type=qq'),
    ),
    'scene'=>'',
    'bank_code'=>''
);