<?php
$settings = array(
    'user' => array(
        'href' => '#',
        'name' => '用户',
        'icon' => 'fa fa-user',
    ),
    'adminUserList' => array(
        'href' => array(
            'for' => 'adminEntityList',
            'entity' => 'user',
            'page' => 1,
        ),
        'icon' => 'fa fa-list',
        'name' => '用户列表',
    ),
    'adminUserSettings' => array(
        'href' => array(
            'for' => 'adminConfigEdit',
            'contentModel' => 'user',
        ),
        'icon' => 'fa fa-cogs',
        'name' => '设置',
    ),
);
