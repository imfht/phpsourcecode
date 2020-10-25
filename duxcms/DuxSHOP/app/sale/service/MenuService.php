<?php
namespace app\sale\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'sale' => array(
                'name' => '推广',
                'icon' => 'carts',
                'order' => 5,
                'menu' => array(
                    array(
                        'name' => '推广设置',
                        'icon' => 'cog',
                        'order' => 1,
                        'menu' => array(
                            array(
                                'name' => '功能设置',
                                'url' => url('sale/Config/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '推广商管理',
                        'icon' => 'user',
                        'order' => 2,
                        'menu' => array(
                            array(
                                'name' => '推广用户',
                                'url' => url('sale/User/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '用户审核',
                                'icon' => 'bars',
                                'url' => url('sale/UserApply/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '推广等级',
                                'icon' => 'users',
                                'url' => url('sale/UserLevel/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '推广管理',
                        'icon' => 'bars',
                        'order' => 3,
                        'menu' => array(
                            array(
                                'name' => '订单管理',
                                'url' => url('sale/Order/index'),
                                'order' => 0
                            ),
                        )
                    ),
                ),
            ),
        );
    }

    /**
     * 获取会员菜单
     * @return array
     */
    public function getMobileMemberMenu() {
        $login = \dux\Dux::cookie()->get('user_login');

        $info = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $login['uid']
        ]);
        if (!$info['agent']) {
            $menu = [
                [
                    'name' => '推广申请',
                    'url' => url('sale/Apply/index'),
                    'icon' => 'code-fork',
                    'order' => 1
                ],
            ];
        } else {

            $menu = [
                [
                    'name' => '推广信息',
                    'url' => url('sale/Info/index'),
                    'icon' => 'code-fork',
                    'order' => 0

                ],
                [
                    'name' => '推广码',
                    'url' => url('sale/Qrcode/index'),
                    'icon' => 'qrcode',
                    'order' => 1

                ],
                [
                    'name' => '推广订单',
                    'url' => url('sale/Order/index'),
                    'icon' => 'bookmark',
                    'order' => 2

                ],
                [
                    'name' => '我的会员',
                    'url' => url('sale/UserHas/index'),
                    'icon' => 'cc',
                    'order' => 3

                ]

            ];
        }

        return [
            'sale' => [
                'name' => '商城推广',
                'order' => 102,
                'menu' => $menu
            ]
        ];
    }

    public function getMemberMenu() {
        $login = \dux\Dux::cookie()->get('user_login');

        $info = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $login['uid']
        ]);
        if (!$info['agent']) {
            $menu = [
                [
                    'name' => '推广申请',
                    'url' => url('sale/Apply/index'),
                    'icon' => 'code-fork',
                    'order' => 1
                ],
            ];
        } else {

            $menu = [
                [
                    'name' => '推广信息',
                    'url' => url('sale/Info/index'),
                    'icon' => 'code-fork',
                    'order' => 0

                ],
                [
                    'name' => '推广码',
                    'url' => url('sale/Qrcode/index'),
                    'icon' => 'qrcode',
                    'order' => 1

                ],
                [
                    'name' => '推广订单',
                    'url' => url('sale/Order/index'),
                    'icon' => 'bookmark',
                    'order' => 2

                ],
                [
                    'name' => '我的会员',
                    'url' => url('sale/UserHas/index'),
                    'icon' => 'cc',
                    'order' => 3

                ]

            ];
        }

        return [
            'sale' => [
                'name' => '推广',
                'desc' => '商品推广',
                'order' => 99,
                'menu' => $menu
            ]
        ];
    }


}

