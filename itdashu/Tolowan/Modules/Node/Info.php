<?php
$settings = array(
    'name' => '文章',
    'machine' => 'node',
    'uninstall' => false,
    'project' => 'core',
    'description' => 'Tolowan文章管理组件。',
    'handle' => array(
        'setting' => array(
            'for' => 'adminConfig',
            'contentModel' => 'node',
        ),
        'install' => false,
        'startUsing' => false,
        'stopUsing' => false,
        'uninstall' => false,
        'delete' => false,
    ),
);
