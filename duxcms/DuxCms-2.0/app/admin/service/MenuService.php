<?php
namespace app\admin\service;
/**
 * 后台菜单接口
 */
class MenuService{
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
                'order' => 9,
                'menu' => array(
                    array(
                        'name' => '系统设置',
                        'icon' => 'sliders',
                        'url' => url('admin/Setting/site'),
                        'order' => 0,
                        'divider' => true,
                    ),
                    array(
                        'name' => '缓存管理',
                        'icon' => 'upload',
                        'url' => url('admin/Manage/cache'),
                        'order' => 3,
                        'divider' => true,
                    ),
                    array(
                        'name' => '应用管理',
                        'icon' => 'flash',
                        'url' => url('admin/Functions/index'),
                        'order' => 4,
                        'divider' => true,
                    ),
                    array(
                        'name' => '安全记录',
                        'icon' => 'qrcode',
                        'url' => url('admin/AdminLog/index'),
                        'order' => 5
                    ),
                    array(
                        'name' => '备份还原',
                        'icon' => 'database',
                        'url' => url('admin/AdminBackup/index'),
                        'order' => 6
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
