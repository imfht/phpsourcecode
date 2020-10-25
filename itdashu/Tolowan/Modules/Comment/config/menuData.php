<?php
$settings = array(
    'comment' => array(
        'href' => '#',
        'name' => '评论',
        'icon' => 'fa fa-commenting',
    ),
    'adminComment' => array(
        'href' => array(
            'for' => 'adminEntityList',
            'entity' => 'comment',
            'page' => 1,
        ),
        'name' => '评论列表',
        'icon' => 'fa fa-fw fa-list-alt',
    ),
    'adminCommentSettings' => array(
        'href' => array(
            'for' => 'adminConfigEdit',
            'contentModel' => 'comment',
        ),
        'icon' => 'fa fa-gears',
        'name' => '评论设置',
    ),
);
