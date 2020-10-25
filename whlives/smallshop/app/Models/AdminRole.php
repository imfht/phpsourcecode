<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/11/22
 * Time: 3:33 PM
 */

namespace App\Models;

/**
 * 管理员角色
 * Class AdminRole
 * @package App\Models
 */
class AdminRole extends BaseModels
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'admin_role';
    protected $guarded = ['id'];

    /**
     * 获取用户角色的具体权限
     * @param $role_id 角色id
     * @param $is_refresh 是否刷新缓存
     */
    public static function adminRight($role_id, $is_refresh = 0)
    {
        $role_id = (int)$role_id;
        if (!$role_id) return false;
        $return = cache('admin_user_role:' . $role_id);
        if (!$return || $is_refresh) {
            $return = array(
                'menu_top' => [],
                'menu_child' => [],
                'menus' => []
            );
            $role = self::where('id', $role_id)->value('right');
            if (!$role) {
                return $return;
            }
            $role_right = json_decode($role, true);

            $return['menu_top'] = array_keys($role_right);
            $menus = $menu_child = $right_ids = array();
            foreach ($role_right as $group_menu) {
                foreach ($group_menu as $key => $menu) {
                    $menu_child[] = $key;
                    foreach ($menu as $right) {
                        $_right = explode(',', $right);
                        $right_ids = array_merge($_right, $right_ids);
                    }
                }
            }
            if ($right_ids) {
                $rights = AdminRight::where('status', AdminRight::STATUS_ON)->whereIn('id', $right_ids)->pluck('right');
                if ($rights) {
                    foreach ($rights as $val) {
                        $_item = explode(',', $val);
                        $menus = array_merge($_item, $menus);
                    }
                }
            }
            $return['menu_child'] = $menu_child;
            $return['menus'] = array_unique($menus);
            cache(['admin_user_role:' . $role_id => $return], 600);
        }
        return $return;
    }
}
