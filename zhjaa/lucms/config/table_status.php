<?php

return [
    'users' => [
        'enable' => ['F' => '禁用', 'T' => '启用'],
        'is_admin' => ['F' => '否', 'T' => '是'],
    ],
    'attachments' => [
        'enable' => ['F' => '禁用', 'T' => '启用'],
        'use_status' => ['F' => '未使用', 'T' => '使用中'],
        'type' => ['avatars' => '头像', 'advertisements' => '广告', 'editors' => '编辑器', 'tmp' => '临时图片', 'carousels' => '轮播图', 'versions' => 'app 版本包'],
        'storage_position' => ['local' => '本地', 'oss' => '啊里云 oss', 'api_local' => 'api服务器', 'api_oss' => 'api服务器oss'],
    ],
    'advertisement_positions' => [
        'type' => ['default' => '普通广告位', 'model' => '模型跳转广告位', 'spa' => '单页面广告位'],
    ],
    'sms' => [
        'type' => ['R' => '注册短信', 'L' => '登录短信', 'I' => '通讯录邀请短信', 'O' => '其它短信'],
    ],
    'advertisements' => [
        'enable' => ['F' => '禁用', 'T' => '启用'],
    ],
    'articles' => [
        'enable' => ['F' => '禁用', 'T' => '启用'],
        'recommend' => ['F' => '不推荐', 'T' => '推荐'],
        'top' => ['F' => '不置顶', 'T' => '置顶'],
        'access_type' => ['PUB' => '公共访问', 'PRI' => '私密', 'PWD' => '密码访问'],
    ],
    'ip_filters' => [
        'type' => ['white' => '白名单', 'black' => '黑名单'],
    ],
    'alipay_notifies' => [
        'model_type' => ['undefined' => '未定义表', 'pay_test' => '支付测试']
    ],
    'pay_notify_raws' => [
        'model_type' => ['ali_charge' => '支付宝通知', 'wx_charge' => '微信支付通知', 'cmb_charge' => '招商支付通知']
    ],
    'system_configs' => [
        'enable' => ['F' => '禁用', 'T' => '启用'],
    ],
    'admin_messages' => [
        'status' => ['U' => '未读', 'R' => "已读"],
        'type' => ['SY' => '系统消息', 'FK' => "意见反馈"],
    ],
    'api_messages' => [
        'status' => ['U' => '未读', 'R' => "已读"],
        'is_alert_at_home' => ['F' => '不弹出', 'T' => "弹出"],
        'type' => ['SY' => '系统消息'],
    ],
    'api_versions' => [
        'port' => ['A' => 'App'],
        'system' => ['ANDROID' => 'android', 'IOS' => "ios", 'ALL' => '通用'],
    ],
    'logs' => [
        'type' => ['C' => '添加', 'U' => '更新', 'R' => '读取', 'D' => '删除', 'L' => '登录', 'O' => '其它'],
        'table_name' => [
            'users' => '用户表', 'attachments' => '附件表', 'roles' => '角色表', 'permissions' => '权限表',
//            'advertisement_positions' => '广告位表', 'advertisements' => '广告表', 'categories' => '分类表', 'tags' => '标签表',
//            'articles' => '文章表', 'ip_filters' => 'ip 过滤表', 'alipay_notifies' => '支付宝异步通知表', 'pay_notify_raws' => '支付异步通知数据源表',
//            'system_configs' => '系统配置表'
        ],
    ]
];
