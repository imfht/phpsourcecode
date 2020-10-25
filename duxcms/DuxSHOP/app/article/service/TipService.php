<?php
namespace app\article\service;

/**
 * 标签生成接口
 */
class TipService {


	public function getSiteTip(){

        $classList = target('article/ArticleClass')->loadTreeList();

        $classData = [];
        if($classList) {
            foreach($classList as $vo) {
                $classData[$vo['class_id']] = $vo['cname'];
            }
        }

        return [
            'article' => [
                'name' => '文章标签',
                'order' => 10,
                'list' => [
                    [
                        'name' => '文章栏目',
                        'help' => '本标签用于所有模板页面',
                        'loop' => true,
                        'label' => html_in("<!--{sign}{app=\"article\" label=\"classList\" {attr}}-->\n{html}\n<!--{/{sign}}-->"),
                        'sign' => 'list',
                        'attr' => [
                            'parent_id' => [
                                'name' => '上级栏目ID',
                                'type' => 'select',
                                'list' =>  $classData,
                            ],
                            'class_id' => [
                                'name' => '栏目ID',
                                'type' => 'select-multiple',
                                'list' => $classData,
                                'repay' => '',
                            ],
                            'limit' => [
                                'name' => '数量',
                                'type' => 'int',
                                'help' => '输入纯数字'
                            ]

                        ],
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
