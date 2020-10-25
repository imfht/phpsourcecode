<?php
namespace app\mall\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'shop' => array(
                'menu' => array(
                    array(
                        'name' => '普通商品',
                        'icon' => 'hdd-o',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '商品管理',
                                'icon' => 'bars',
                                'url' => url('mall/Content/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '商品分类',
                                'icon' => 'code-fork',
                                'url' => url('mall/Class/index'),
                                'order' => 1
                            ),
                        )
                    ),
                ),
            ),
            'order' => array(
                'menu' => array(
                    array(
                        'name' => '商品',
                        'icon' => 'bars',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '订单管理',
                                'icon' => 'bars',
                                'url' => url('mall/Order/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '订单统计',
                                'icon' => 'bars',
                                'url' => url('mall/OrderStatis/index'),
                                'order' => 1
                            ),
                        )
                    ),
                ),
            ),
        );
    }
}

