<?php
namespace app\site\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        $formList = target('site/SiteForm')->loadList();
        $formMenu = [];
        if(!empty($formList)) {
            foreach($formList as $vo) {
                $formMenu[] = [
                    'name' => $vo['name'],
                    'order' => 0,
                    'menu' => array(
                        array(
                            'name' => '列表',
                            'icon' => 'bank',
                            'url' => url('site/Form/' . $vo['label']),
                            'order' => 0
                        ),
                    )
                ];
            }
        }
        return array(
            'content' => array(
                'name' => '内容',
                'icon' => 'newspaper-o',
                'order' => 1,
                'menu' => array(
                ),
            ),
            'form' => array(
                'name' => '表单',
                'icon' => 'newspaper-o',
                'order' => 97,
                'menu' => $formMenu,
            ),
            'site' => array(
                'name' => '站点',
                'icon' => 'bank',
                'order' => 98,
                'menu' => array(
                    array(
                        'name' => '设置',
                        'icon' => 'cog',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '站点设置',
                                'icon' => 'bank',
                                'url' => url('site/Config/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '导航',
                        'icon' => 'bars',
                        'order' => 1,
                        'menu' => array(
                            array(
                                'name' => '导航管理',
                                'icon' => 'bars',
                                'url' => url('site/Nav/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '导航分组',
                                'icon' => 'code-fork',
                                'url' => url('site/NavGroup/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '表单',
                        'order' => 2,
                        'icon' => 'newspaper-o',
                        'menu' => array(
                            array(
                                'name' => '表单管理',
                                'icon' => 'bars',
                                'url' => url('site/FormManage/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '字段配置',
                                'icon' => 'cogs',
                                'url' => url('site/FormField/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '模型',
                        'order' => 3,
                        'icon' => 'inbox',
                        'menu' => array(
                            array(
                                'name' => '模型管理',
                                'icon' => 'bars',
                                'url' => url('site/ModelManage/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '字段配置',
                                'icon' => 'cogs',
                                'url' => url('site/ModelField/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '筛选',
                        'order' => 4,
                        'icon' => 'filter',
                        'menu' => array(
                            array(
                                'name' => '筛选管理',
                                'icon' => 'bars',
                                'url' => url('site/Filter/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '推荐位',
                        'order' => 5,
                        'icon' => 'bookmark',
                        'menu' => array(
                            array(
                                'name' => '推荐位管理',
                                'icon' => 'bars',
                                'url' => url('site/Position/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '碎片',
                        'order' => 6,
                        'icon' => 'dot-circle-o',
                        'menu' => array(
                            array(
                                'name' => '碎片管理',
                                'icon' => 'bars',
                                'url' => url('site/Fragment/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '广告',
                        'order' => 7,
                        'icon' => 'road',
                        'menu' => array(
                            array(
                                'name' => '广告管理',
                                'icon' => 'bars',
                                'url' => url('site/Adv/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '广告位',
                                'icon' => 'bars',
                                'url' => url('site/AdvPosition/index'),
                                'order' => 1
                            ),
							array(
                                'name' => '广告区域',
                                'icon' => 'bars',
                                'url' => url('site/AdvRegion/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '搜索',
                        'order' => 8,
                        'icon' => 'search',
                        'menu' => array(
                            array(
                                'name' => '搜索管理',
                                'icon' => 'bars',
                                'url' => url('site/Search/index'),
                                'order' => 0
                            ),
                        )
                    ),
                ),
            ),
        );
    }
}

