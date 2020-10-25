<?php
namespace app\admin\service;
/**
 * 后台菜单接口
 */
class Menu{
	/**
	 * 获取菜单结构
	 */
	public function getAdminMenu(){
		return array(
            'index' => array(
                'name' => '首页',
                'icon' => 'home',
                'order' => 0,
                'menu' => array(
                    array(
                        'name' => '管理首页',
                        'icon' => 'home',
                        'url' => url('admin/Index/home'),
                        'order' => 0
                    )
                )
            ),
            'system' => array(
                'name' => '系统',
                'icon' => 'tachometer',
                'order' => 4,
                'menu' => array(
                    array(
                        'name' => '系统设置',
                        'icon' => 'sliders',
                        'url' => url('admin/Setting/site'),
                        'order' => 0,
                        'divider' => true,
                    ),
                    array(
                        'name' => '用户管理',
                        'icon' => 'user',
                        'url' => url('admin/AdminUser/index'),
                        'order' => 7,
                        'divider' => true,
                    ),
                    array(
                        'name' => '用户组管理',
                        'icon' => 'group',
                        'url' => url('admin/AdminUserGroup/index'),
                        'order' => 8,
                    )
                )
            ),
        );
	}
	


}
