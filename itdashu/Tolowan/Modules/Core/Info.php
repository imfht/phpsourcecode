<?php
$settings = array(
    'name' => '核心组件',
    'machine' => 'Core',
    'require' => array('User', 'Form'),
    'conflict' => array('system'),
    'uninstall' => false,
    'remove' => false,
    'project' => 'core',
    'description' => 'Tolowan管理后台核心组件，不可禁用，不可替换。',
    'handle' => array(
        'setting' => array(
            'for' => 'adminConfig',
        ),
        'install' => false,
        'startUsing' => false,
        'stopUsing' => false,
        'uninstall' => false,
        'delete' => false,
    ),
);
