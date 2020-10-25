<?php
return [
    [
        'name' => '网站设计',
        'icon' => 'edit',
        'menu' => [
            ['name' => '模板管理','url' => url('system/cms.themes/index')],
        ]
    ],
    [
        'name' => '系统配置',
        'icon' => 'setup_icon',
        'menu' => [
            ['name' => '管理员','url' => url('system/admin.user/index')],
            ['name' => '站点管理','url' => url('system/admin.setting/webConfig')],
        ]
    ]  
];

