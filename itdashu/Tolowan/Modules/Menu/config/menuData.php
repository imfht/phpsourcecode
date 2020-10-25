<?php
$settings = array(
    'menu' => array(
        'href' => '#',
        'name' => '菜单',
        'icon' => 'fa fa-sitemap'
    ),
    'adminMenu' => array(
        'href' => array(
            'for' => 'adminConfigList',
            'contentModel' => 'menu'
        ),
        'icon' => 'fa fa-gears',
        'name' => '菜单管理'
    )
);
