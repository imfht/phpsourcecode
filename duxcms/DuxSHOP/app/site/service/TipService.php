<?php
namespace app\site\service;

/**
 * 标签生成接口
 */
class TipService {


	public function getSiteTip(){
        return [
            'site' => [
                'name' => '站点标签',
                'order' => 0,
                'list' => [
                    [
                        'name' => '页面标题',
                        'help' => '本标签用于所有模板页面',
                        'loop' => false,
                        'label' => '{$pageInfo.title}',
                    ],
                    [
                        'name' => '当前位置',
                        'help' => '本标签用于所有模板页面',
                        'loop' => true,
                        'label' => html_in('<!--loop{$crumb as ${sign}}-->{html}<!--{/loop}-->'),
                        'sign' => 'vo',
                        'content' => [
                            [
                                'name' => '名称',
                                'label' => 'name',
                            ],
                            [
                                'name' => '链接',
                                'label' => 'url',
                            ]
                        ]
                    ]
                ]
            ]

        ];
	}

}
