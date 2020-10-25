<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

return array(
    //  设置cookie信息
    'cookie_domain' => '.wenlong.org',
    'cookie_path' => '/',

    //  设置修改php.ini的配置
    'php.ini' => array(
        'date.timezone' => 'Asia/Shanghai',
        'session.save_path' => DIR.'session'
    )
);
