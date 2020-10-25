<?php

return array(
    'code'      => 'wechat',
    'name'      => '微信登录',
    'desc'      => '申请地址：http://open.weixin.qq.com/',
    'author'    => 'WKCMS',
    'version'   => '1.0',
    'config'    => array(
        'app_key'   => array(
            'text'  => 'App Key',
            'desc'  => '申请：https://open.weixin.qq.com/',
            'type'  => 'text',
        ),
        'app_secret'=> array(
            'text'  => 'App Secret',
            'desc'  => '申请：https://open.weixin.qq.com/',
            'type'  => 'text',
        )
    )
);