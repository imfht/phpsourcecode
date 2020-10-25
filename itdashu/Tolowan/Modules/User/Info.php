<?php
$settings = array(
    'name' => '用户',
    'machine' => 'user',
    'project' => 'core',
    'description' => 'Tolowan用户组件。',
    'handle' => array(
        'setting' => array(
            'for' => 'adminConfig',
            'contentModel' => 'user',
        ),
        'install' => false,
        'startUsing' => false,
        'stopUsing' => false,
        'uninstall' => false,
        'delete' => false,
    ),
);
