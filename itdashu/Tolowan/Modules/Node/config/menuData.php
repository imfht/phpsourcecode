<?php

$settings = array(
    'adminNode' => array(
        'href' => '#',
        'name' => '内容',
        'icon' => 'fa fa-newspaper-o',
    ),
    'adminNodeList' => array(
        'href' => array(
            'for' => 'adminEntityList',
            'entity' => 'node',
            'page' => 1,
        ),
        'name' => '内容列表',
        'icon' => 'fa fa-fw fa-list-alt',
    ),
    'adminNodeSettings' => array(
        'href' => array(
            'for' => 'adminConfigEdit',
            'contentModel' => 'node',
        ),
        'icon' => 'fa fa-cogs',
        'name' => '内容设置',
    ),
);
