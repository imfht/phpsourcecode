<?php
$settings = array(
    'name' => 'Common',
    'machine' => 'Core',
    'require' => array(),
    'conflict' => array(),
    'project' => 'core',
    'description' => '核心评论组件',
    'handle' => array(
        'setting' => array(
            'for' => 'adminConfigEdit',
            'contentModel' => 'comment',
        ),
        'install' => false,
        'startUsing' => false,
        'stopUsing' => false,
        'uninstall' => false,
        'delete' => false,
    ),
);
