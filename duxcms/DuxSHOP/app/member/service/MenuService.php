<?php

namespace app\member\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'member' => array(
                'name' => '会员',
                'icon' => 'user',
                'order' => 98,
                'menu' => array(
                    array(
                        'name' => '会员',
                        'icon' => 'users',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '会员管理',
                                'icon' => 'user',
                                'url' => url('member/MemberUser/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '角色管理',
                                'icon' => 'group',
                                'url' => url('member/MemberRole/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '会员等级',
                                'icon' => 'group',
                                'url' => url('member/MemberGrade/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '实名制管理',
                                'icon' => 'credit-card',
                                'url' => url('member/MemberReal/index'),
                                'order' => 3
                            ),
                        )
                    ),
                    array(
                        'name' => '财务',
                        'icon' => 'money',
                        'order' => 1,
                        'menu' => array(
                            array(
                                'name' => '财务账户',
                                'icon' => 'credit-card',
                                'url' => url('member/PayAccount/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '收支记录',
                                'icon' => 'bars',
                                'url' => url('member/PayLog/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '充值记录',
                                'icon' => 'bars',
                                'url' => url('member/PayRecharge/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '银行管理',
                                'icon' => 'bank',
                                'url' => url('member/PayBank/index'),
                                'order' => 3
                            ),
                            array(
                                'name' => '银行卡管理',
                                'icon' => 'credit-card',
                                'url' => url('member/PayCard/index'),
                                'order' => 4
                            ),
                            array(
                                'name' => '提现管理',
                                'icon' => 'deviantart',
                                'url' => url('member/PayCash/index'),
                                'order' => 5
                            ),
                        )
                    ),
                    array(
                        'name' => '积分',
                        'icon' => 'database',
                        'order' => 2,
                        'menu' => array(
                            array(
                                'name' => '积分账户',
                                'icon' => 'credit-card',
                                'url' => url('member/PointsAccount/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '积分记录',
                                'icon' => 'bars',
                                'url' => url('member/PointsLog/index'),
                                'order' => 1
                            ),
                        )
                    ),
                    array(
                        'name' => '设置',
                        'icon' => 'cog',
                        'order' => 99,
                        'menu' => array(
                            array(
                                'name' => '基本设置',
                                'icon' => 'cog',
                                'url' => url('member/MemberConfig/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '配置管理',
                                'icon' => 'bars',
                                'url' => url('member/MemberConfigManage/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '验证码',
                                'icon' => 'qrcode',
                                'url' => url('member/MemberVerify/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '支付接口',
                                'icon' => 'bars',
                                'url' => url('member/PayConf/index'),
                                'order' => 3
                            ),
                        )
                    ),
                ),
            ),
        );
    }

    public function getMobileMemberMenu() {
        return [
            'member' => [
                'name' => '会员信息',
                'order' => 98,
                'menu' => [
                    [
                        'name' => '我的钱包',
                        'icon_img' => ROOT_URL . '/public/member/icon/finance.png',
                        'url' => url('member/Finance/index'),
                        'order' => 10
                    ],
                    [
                        'name' => '我的积分',
                        'icon_img' => ROOT_URL . '/public/member/icon/point.png',
                        'url' => url('member/Points/index'),
                        'order' => 12
                    ],
                    [
                        'name' => '修改密码',
                        'icon_img' => ROOT_URL . '/public/member/icon/password.png',
                        'url' => url('member/setting/password'),
                        'order' => 14
                    ],
                    [
                        'name' => '个人资料',
                        'icon_img' => ROOT_URL . '/public/member/icon/setting.png',
                        'url' => url('member/Setting/index'),
                        'order' => 16
                    ],
                    [
                        'name' => '实名认证',
                        'icon_img' => ROOT_URL . '/public/member/icon/real.png',
                        'url' => url('member/real/index'),
                        'order' => 18
                    ],
                    [
                        'name' => '银行卡',
                        'icon_img' => ROOT_URL . '/public/member/icon/card.png',
                        'url' => url('member/Card/index'),
                        'order' => 20
                    ],

                    [
                        'name' => '充值',
                        'icon_img' => ROOT_URL . '/public/member/icon/recharge.png',
                        'url' => url('member/Recharge/index'),
                        'order' => 21
                    ],
                    [
                        'name' => '提现',
                        'icon_img' => ROOT_URL . '/public/member/icon/cash.png',
                        'url' => url('member/Cash/index'),
                        'order' => 22
                    ],
                ]
            ],
            'tools' => [
                'name' => '实用工具',
                'order' => 99,
                'menu' => [
                ]
            ],

            'setting' => [
                'name' => '设置',
                'desc' => '账户设置',
                'icon' => 'cog',
                'order' => 100,
                'menu' => [
                    [
                        'name' => '资料修改',
                        'url' => url('member/setting/index'),
                        'icon' => 'user',
                        'order' => 90
                    ],
                    [
                        'name' => '修改头像',
                        'url' => url('member/setting/avatar'),
                        'icon' => 'photo',
                        'order' => 91
                    ],
                    [
                        'name' => '登录密码',
                        'url' => url('member/setting/password'),
                        'icon' => 'lock',
                        'order' => 92
                    ],
                    [
                        'name' => '支付密码',
                        'icon' => 'lock',
                        'url' => url('member/setting/payPassword'),
                        'order' => 93
                    ],
                    [
                        'name' => '银行卡管理',
                        'url' => url('member/card/index'),
                        'icon' => 'photo',
                        'order' => 94
                    ],
                    [
                        'name' => '实名认证',
                        'url' => url('member/real/index'),
                        'icon' => 'photo',
                        'order' => 95
                    ],
                ]
            ]

        ];
    }

    /**
     * 获取会员菜单
     * @return array
     */
    public function getMemberMenu() {
        return [
            'bank' => [
                'name' => '账户',
                'desc' => '账户管理',
                'icon' => 'bank',
                'order' => 98,
                'menu' => [
                    [
                        'name' => '我的钱包',
                        'url' => url('member/Finance/index'),
                        'order' => 0
                    ],
                    [
                        'name' => '我的积分',
                        'url' => url('member/Points/index'),
                        'order' => 1
                    ],
                ]
            ],
            'setting' => [
                'name' => '设置',
                'desc' => '账号设置',
                'icon' => 'cog',
                'order' => 99,
                'menu' => [
                    [
                        'name' => '银行卡管理',
                        'url' => url('member/card/index'),
                        'icon' => 'photo',
                        'order' => 90
                    ],
                    [
                        'name' => '资料修改',
                        'url' => url('member/setting/index'),
                        'icon' => 'user',
                        'order' => 91
                    ],
                    [
                        'name' => '修改头像',
                        'url' => url('member/setting/avatar'),
                        'icon' => 'photo',
                        'order' => 92
                    ],
                    [
                        'name' => '修改密码',
                        'url' => url('member/setting/password'),
                        'icon' => 'lock',
                        'order' => 93
                    ],
                ]
            ]

        ];
    }
}

