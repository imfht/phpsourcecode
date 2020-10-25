<?php
namespace app\system\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'index' => array(
                'name' => '首页',
                'icon' => 'home',
                'order' => 0,
                'menu' => array(
                    array(
                        'name' => '信息',
                        'icon' => 'dashboard',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '系统概况',
                                'icon' => 'dashboard',
                                'url' => url('system/Index/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '系统通知',
                                'icon' => 'bell',
                                'url' => url('system/Notice/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '系统更新',
                                'icon' => 'refresh',
                                'url' => url('system/Update/index'),
                                'order' => 2
                            ),
                        )
                    ),
                ),
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'build',
                'order' => 99,
                'menu' => array(
                    array(
                        'name' => '设置',
                        'icon' => 'cog',
                        'order' => 10,
                        'menu' => array(
                            array(
                                'name' => '系统设置',
                                'icon' => 'cog',
                                'url' => url('system/Config/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '自定义配置',
                                'icon' => 'bars',
                                'url' => url('system/ConfigManage/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '上传驱动',
                                'icon' => 'cog',
                                'url' => url('system/ConfigUpload/index'),
                                'order' => 3
                            ),
                            array(
                                'name' => 'API接口',
                                'icon' => 'cog',
                                'url' => url('system/ConfigApi/index'),
                                'order' => 4
                            ),
                        )
                    ),
                    array(
                        'name' => '管理员',
                        'icon' => 'users',
                        'order' => 11,
                        'menu' => array(
                            array(
                                'name' => '用户管理',
                                'icon' => 'user',
                                'url' => url('system/User/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '角色管理',
                                'icon' => 'group',
                                'url' => url('system/Role/index'),
                                'order' => 5
                            )
                        )
                    ),
                    array(
                        'name' => '应用',
                        'icon' => 'cubes',
                        'order' => 12,
                        'menu' => array(
                            array(
                                'name' => '应用管理',
                                'icon' => 'cubes',
                                'url' => url('system/Application/index'),
                                'order' => 1
                            ),
                        )
                    ),

                ),
            ),
        );
    }
}

