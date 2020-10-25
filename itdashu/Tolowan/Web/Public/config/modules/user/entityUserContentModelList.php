<?php $settings = array(
    'admin' => array(
        'machine' => 'admin',
        'modelName' => '超级管理员',
        'description' => '超级管理员',
        'fields' => array(),
    ),
    'user' => array(
        'machine' => 'user',
        'modelName' => '用户',
        'description' => '普通注册用户',
        'fields' => 'm.user.field_user',
    ),
    'anonymous' => array(
        'machine' => 'anonymous',
        'modelName' => '匿名用户',
        'description' => '匿名用户',
        'fields' => array(),
    ),
);