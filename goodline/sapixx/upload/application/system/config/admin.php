<?php
return [
    [
        'name' => '应用管理',
        'icon' => 'my_icon',
        'menu' => [
            ['name' => '客户应用','url' => url('system/admin.miniapp/authorizar'),'icon' => 'yingyongyuanma'],
            ['name' => '用户管理','url' => url('system/admin.member/index'),'icon' => 'my_icon'],
            ['name' => '应用管理','url' => url('system/admin.miniapp/index'),'icon' => 'setup_icon'],
        ]
    ],
    [
        'name' => '微信服务',
        'icon' => 'weixin',
        'menu' => [
            ['name' => '开放平台', 'url' => url('system/admin.setting/wechatOpen')],
            ['name' => '微信支付', 'url' => url('system/admin.setting/wechatPay')],
            ['name' => '扫码登录', 'url' => url('system/admin.setting/wechatAccount')],
            ['name' => '云市场','url' => url('system/admin.memberCloud/index')],
        ]
    ],
    [
        'name' => '阿里云服务',
        'icon' => 'data',
        'menu' => [
            ['name' => '短信服务','url' => url('system/admin.setting/aliSms')],
            ['name' => '云市场','url' => url('system/admin.setting/aliApi')]
        ]
    ],
    [
        'name' => '基础设置',
        'icon' => 'setup_icon',
        'menu' => [
            ['name' => '远程附件','url' => url('system/admin.setting/aliOss')],
        ]
    ]
];
