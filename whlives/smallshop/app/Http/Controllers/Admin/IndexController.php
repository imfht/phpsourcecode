<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/16
 * Time: 3:33 PM
 */

namespace App\Http\Controllers\Admin;

use App\Models\AdminRole;
use App\Models\Menu;
use Illuminate\Http\Request;

class IndexController extends BaseController
{

    /**
     * 后台右侧首页
     */
    public function main()
    {
        $data = array(
            'http_host' => $_SERVER["HTTP_HOST"],//网站域名
            'datetime' => get_date(),//服务器时间
            'file_size' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled"//文件最大上传限制
        );
        return $this->success($data);
    }

    /**
     * 获取管理菜单
     * @param Request $request
     * @return array|void
     * @throws \App\Exceptions\ApiException
     */
    public function leftMenu()
    {
        $user_data = $this->getUserInfo();
        $menus = Menu::getMenu();
        $admin_menu = array();
        //读取菜单权限
        if ($user_data['role_id'] == 1) {
            $admin_menu = $menus;
        } else {
            $role_right = AdminRole::adminRight($user_data['role_id']);//权限
            foreach ($menus as $menu_top) {
                if (in_array($menu_top['id'], $role_right['menu_top'])) {
                    $_menu_child = array();
                    if (isset($menu_top['list'])) {
                        foreach ($menu_top['list'] as $menu_child) {
                            if (in_array($menu_child['id'], $role_right['menu_child'])) {
                                $_menu_child[] = $menu_child;
                            }
                        }
                        $menu_top['list'] = $_menu_child;
                    }
                    $_menus[] = $menu_top;
                }
            }
            $admin_menu = $_menus;
        }
        return $this->success($admin_menu);
    }
}
