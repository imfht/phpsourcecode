<?php

$config = array();

$config['product_name'] = 'XX传媒集团CMS管理后台';
$config['product_release_ver'] = '1.0.1 Build 20150925';

$config['cookiePre'] = 'democmsadmin_';
$config['hashSalt'] = 'asdfasdfm90by8dsfgrty6ui8i7tq2=,';

$config['controller_allow_no_login_action'] = array(
    'user/login/index',
    'user/login/bytaobaooauth',
    'user/login/bytaobaooauthcallback',
);

return $config;