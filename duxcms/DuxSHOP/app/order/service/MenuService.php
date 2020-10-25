<?php
namespace app\order\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'order' => array(
                'name' => '订单',
                'icon' => 'carts',
                'order' => 3,
                'menu' => array(
                    array(
                        'name' => '设置',
                        'icon' => 'cog',
                        'order' => 98,
                        'menu' => array(
                            array(
                                'name' => '订单设置',
                                'icon' => 'cog',
                                'url' => url('order/Config/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '物流列表',
                                'icon' => 'bars',
                                'url' => url('order/ConfigExpress/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '运费模板',
                                'icon' => 'bars',
                                'url' => url('order/ConfigDelivery/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '物流接口',
                                'icon' => 'bars',
                                'url' => url('order/ConfigWaybill/index'),
                                'order' => 3
                            ),
                        )
                    ),
                    array(
                        'name' => '售后',
                        'icon' => 'list-alt',
                        'order' => 99,
                        'menu' => array(
                            array(
                                'name' => '退款管理',
                                'icon' => 'bars',
                                'url' => url('order/Refund/index'),
                                'order' => 0
                            ),
                        )
                    ),
                    array(
                        'name' => '配送',
                        'icon' => 'truck',
                        'order' => 100,
                        'menu' => array(
                            array(
                                'name' => '配货管理',
                                'icon' => 'bars',
                                'url' => url('order/Parcel/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '发货管理',
                                'icon' => 'bars',
                                'url' => url('order/Delivery/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '收款管理',
                                'icon' => 'bars',
                                'url' => url('order/Receipt/index'),
                                'order' => 3
                            ),
                            array(
                                'name' => '自提点管理',
                                'icon' => 'bars',
                                'url' => url('order/Take/index'),
                                'order' => 4
                            ),
                        )
                    ),
                    array(
                        'name' => '发票',
                        'order' => 101,
                        'icon' => 'ticket',
                        'menu' => array(
                            array(
                                'name' => '发票管理',
                                'icon' => 'bars',
                                'url' => url('order/Invoice/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '发票分类',
                                'icon' => 'bars',
                                'url' => url('order/InvoiceClass/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '优惠券',
                        'order' => 102,
                        'icon' => 'ticket',
                        'menu' => array(
                            array(
                                'name' => '优惠券管理',
                                'icon' => 'bars',
                                'url' => url('order/Coupon/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '优惠券分类',
                                'icon' => 'bars',
                                'url' => url('order/CouponClass/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '领取记录',
                                'icon' => 'bars',
                                'url' => url('order/CouponLog/index'),
                                'order' => 2
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
        return [
            'member' => [
                'menu' => [
                ]
            ],

            'tools' => [
                'menu' => [
                    [
                        'name' => '收货地址',
                        'url' => url('order/Address/index'),
                        'icon_img' => ROOT_URL . '/public/order/icon/address.png',
                        'order' => 20
                    ],
                    [
                        'name' => '购物车',
                        'icon_img' => ROOT_URL . '/public/order/icon/cart.png',
                        'url' => url('member/Recharge/index'),
                        'order' => 21
                    ],
                    [
                        'name' => '优惠券',
                        'url' => url('order/CouponLog/index'),
                        'icon_img' => ROOT_URL . '/public/order/icon/coupon.png',
                        'order' => 22
                    ],
                ]
            ],

        ];
    }

    public function getMemberMenu() {
        return [
            'order' => [
                'name' => '订单',
                'desc' => '订单管理',
                'icon' => 'bank',
                'order' => 98,
                'menu' => [
                    [
                        'name' => '我的订单',
                        'url' => url('order/Order/index'),
                        'order' => 20
                    ],
                    [
                        'name' => '收货地址',
                        'url' => url('order/Address/index'),
                        'order' => 21
                    ],
                    [
                        'name' => '优惠券',
                        'url' => url('order/CouponLog/index'),
                        'order' => 22
                    ],
                ]
            ],

        ];
    }

    /**
     * 获取头部菜单
     * @return array
     */
    public function getMemberHeadMenu() {
        return [
            [
                'name' => '购物车',
                'order' => 99,
                'icon' => 'shopping-cart',
                'url' => url('order/cart/index')
            ]
        ];
    }
}

