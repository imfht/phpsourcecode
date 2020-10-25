<?php
/**
 * 菜单配置
 * User: Administrator
 * Date: 2018/4/24
 * Time: 11:17
 */
return [
    'top'  => [
        'data' => [
            [
                'text' => '首页',
                'icon' => '&#xe68e;',
                'href' => 'demo/welcome.html',
            ],
            [
                'text'   => '常用',
                'icon'   => '&#xe658;',
                'subset' => [
                    [
                        'text' => '表单',
                        'icon' => '&#xe621;',
                        'href' => '/list',
                    ],
                ],
            ]
        ]
    ],
    'left' => [
        'data' => [
            [
                'text'   => '动态交互',
                'icon'   => '&#xe61c;',
                'subset' => [
                    [
                        'text' => '列表',
                        'icon' => '&#xe63c;',
                        'href' => '/list',
                    ],
                    [
                        'text' => '纵向表单',
                        'icon' => '&#xe620;',
                        'href' => '/form',
                    ],
                    [
                        'text' => '横向表单',
                        'icon' => '&#xe620;',
                        'href' => '/xform',
                    ],
                    [
                        'text' => '上传',
                        'icon' => '&#xe620;',
                        'href' => '/upload',
                    ],
                    [
                        'text' => '导出',
                        'icon' => '&#xe620;',
                        'href' => '/excel',
                    ],
                ],
            ],
            [
                'text'   => '各种报表',
                'icon'   => '&#xe61c;',
                'subset' => [
                    [
                        'text' => '常用图型',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/simple.html',
                    ],
                    [
                        'text' => '复合矩形图',
                        'icon' => '&#xe63c;',
                        'href' => 'demo/echart/double.html',
                    ],
                    [
                        'text' => '复合矩形图2',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/top.html',
                    ],
                    [
                        'text' => '线型图',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/line.html',
                    ],
                    [
                        'text' => '复合线型图',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/maxline.html',
                    ],
                    [
                        'text' => '缩放矩形和线型图',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/mix.html',
                    ],
                    [
                        'text' => '缩放线型图',
                        'icon' => '&#xe620;',
                        'href' => 'demo/echart/zoom.html',
                    ],
                ],
            ],
            [
                'text'   => '常用功能',
                'icon'   => '&#xe62e;',
                'subset' => [
                    [
                        'text' => '表单',
                        'icon' => '&#xe63c;',
                        'href' => 'demo/add-edit.html',
                    ],
                    [
                        'text' => '多功能表单',
                        'icon' => '&#xe620;',
                        'href' => 'demo/form.html',
                    ],
                    [
                        'text' => '按扭组',
                        'icon' => '&#xe621;',
                        'href' => 'demo/btn.html',
                    ],
                    [
                        'text' => '弹出窗',
                        'icon' => '&#xe621;',
                        'href' => 'demo/children.html',
                    ],
                    [
                        'text' => '数据表格',
                        'icon' => '&#xe62d;',
                        'href' => 'demo/data-table.html',
                    ],
                    [
                        'text' => '面板手风琴',
                        'icon' => '&#xe621;',
                        'href' => 'demo/folding-panel.html',
                    ],
                    [
                        'text' => '辅助',
                        'icon' => '&#xe621;',
                        'href' => 'demo/auxiliar.html',
                    ],

                ],
            ],
            [
                'text'   => '扩展',
                'icon'   => '&#xe609;',
                'subset' => [
                    [
                        'text' => '登陆',
                        'icon' => '&#xe621;',
                        'href' => 'demo/login.html',
                    ],
                    [
                        'text' => '登陆2',
                        'icon' => '&#xe621;',
                        'href' => 'demo/login2.html',
                    ],
                    [
                        'text' => '登陆3',
                        'icon' => '&#xe621;',
                        'href' => 'demo/login3.html',
                    ],
                    [
                        'text' => '登陆4',
                        'icon' => '&#xe621;',
                        'href' => 'demo/login4.html',
                    ],
                    [
                        'text' => '注册',
                        'icon' => '&#xe621;',
                        'href' => 'demo/register.html',
                    ],
                    [
                        'text' => '统计图表',
                        'icon' => '&#xe621;',
                        'href' => 'demo/map.html',
                    ],
                    [
                        'text' => '进度条',
                        'icon' => '&#xe621;',
                        'href' => 'demo/progress-bar.html',
                    ],

                    [
                        'text' => '选项卡',
                        'icon' => '&#xe621;',
                        'href' => 'demo/tab-card.html',
                    ],
                    [
                        'text' => '静态表格',
                        'icon' => '&#xe62d;',
                        'href' => 'demo/table.html',
                    ],
                    [
                        'text' => '提示',
                        'icon' => '&#xe621;',
                        'href' => 'demo/tips.html',
                    ],
                    [
                        'text' => '树表格',
                        'icon' => '&#xe621;',
                        'href' => 'demo/tree-table.html',
                    ],
                    [
                        'text' => '404页面',
                        'icon' => '&#xe61c;',
                        'href' => 'demo/404.html',
                    ],
                ],
            ]
        ]
    ],
];