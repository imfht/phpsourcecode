<?php
$settings = array(
    'file' => array(
        'href' => '#',
        'name' => '文件',
        'icon' => 'fa fa-fw fa-folder-open-o',
    ),
    'adminFileManage' => array(
        'href' => array(
            'for' => 'adminFileManage',
            'page' => 1,
        ),
        'icon' => 'fa fa-fw fa-hand-spock-o',
        'name' => '文件管理',
    ),
    'adminFileSettings' => array(
        'href' => array(
            'for' => 'adminConfigEdit',
            'contentModel' => 'file',
        ),
        'icon' => 'fa fa-fw fa-gears',
        'name' => '模块设置',
    ),
);
