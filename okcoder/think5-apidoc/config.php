<?php
return [
    'title'         => 'apidoc',                   # 文档title
    'version'       => '3.0',                               # 文档版本
    'copyright'     => 'Powered By OkCoder',          # 版权信息
    'password'      => '',                                  # 访问密码，为空不需要密码
    'qq'            => '1046512080',                        # 咨询QQ
    'document'      => [
        "explain" => [
            'name' => '说明',
            'list' => [
                '登录态'      => ['11'],
                'formId收集' => ['222', '2222'],
                '邀请有礼'     => ['333', '33333', '33333']
            ]
        ],
        "code"    => [
            'name' => '返回码',
            'list' => [
                '0'     => '成功',
                '1'     => '失败'
            ]
        ]
    ],
     // 全局请求header,一般存放token之类的
    'header'        => [

    ],
    // 全局请求参数
    'params'        => [
        '__uid' => 2
    ],
    // 需要生成文档的类
    'controller'    => [
        [
            'name' => 'v2',
            'list' => [
                'api\controller\v2\Open',
                'api\controller\v2\User'
            ]
        ],
        [
            'name' => 'v3',
            'list' => [

            ]
        ]

    ],
    // 过滤、不解析的方法名称
    'filter_method' => [
        '_empty'
    ]
];