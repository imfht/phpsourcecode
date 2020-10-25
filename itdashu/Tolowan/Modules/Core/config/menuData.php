<?php
$settings = array(
    'system' => array(
        'href' => '#',
        'name' => '系统',
        'icon' => 'fa fa-dashboard',
    ),
    'adminCache' => array(
        'href' => array(
            'for' => 'adminCache',
            'handle' => 'list',
            'type' => 'none'
        ),
        'icon' => 'fa fa-fw fa-cubes',
        'name' => '缓存',
    ),
    'adminModules' => array(
        'href' => array(
            'for' => 'adminModules',
        ),
        'icon' => 'fa fa-plug',
        'name' => '模块',
    ),
    'adminThemes' => array(
        'href' => array(
            'for' => 'adminThemes',
        ),
        'icon' => 'fa fa-plug',
        'name' => '主题',
    ),
    'adminSecurity' => array(
        'href' => array(
            'for' => 'adminSecurity',
        ),
        'icon' => 'fa fa-plug',
        'name' => '权限',
    ),
);
