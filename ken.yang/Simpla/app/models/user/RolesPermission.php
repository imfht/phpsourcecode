<?php

/*
 * 角色权限表
 */

class RolesPermission extends Eloquent {

    protected $table = 'roles_permission';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('rid', 'name', 'weight');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    /**
     * 路由过滤权限判断
     */
    public static function check($rid) {
        $as_name = Route::currentRouteName();
        //排除特殊页面的访问
        if (in_array($as_name, array('403', '404', 'login', 'logout', 'register', 'password_remind', 'password_getremind', 'password_reset', 'password_getreset'))) {
            return TRUE;
        }

        $is_role = RolesPermission::where('rid', '=', $rid)->where('name', '=', $as_name)->first();
        if ($is_role) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 获取角色路由权限
     */
    public static function getRoutes() {
        $new_roles_access = array();
        //获取当前用户角色
        $rid = User::find(Auth::user()->id)->roles['rid'];
        //1、获取所有路由
        $roles_access = Hook_access::access();
        //2、权限排除
        if ($roles_access) {
            foreach ($roles_access as $key => $access) {
                //当为匿名用户的时候，排除admin选项
                if ($rid == '1' && stristr($key, 'admin') != '0') {
                    unset($roles_access[$key]);
                    continue;
                }
                if (empty($access['siderbar'])) {
                    continue;
                }
                //添加标题
                if (!isset($new_roles_access[$access['siderbar']])) {
                    $new_roles_access[$access['siderbar']]['title'] = $access['title'];
                    $new_roles_access[$access['siderbar']]['class'] = $access['class'];
                    $new_roles_access[$access['siderbar']]['list'] = array();
                }
                //2、添加列表
                foreach ($access['list'] as $row_key => $row) {
                    //如果是系统管理员，默认获得所有权限
                    if ($rid == 3 && isset($row['weight'])) {
                        //所有条件满足，生产新输出数组
                        $new_roles_access[$access['siderbar']]['list'][$row['weight']] = array(
                            'as' => $row['as'],
                            'title' => $row['title'],
                            'description' => $row['description']
                        );
                        continue;
                    }
                    //判断是否在路由中显示
                    if (!isset($row['weight'])) {
                        unset($roles_access[$key]['list'][$row_key]);
                        continue;
                    }
                    //当有特殊路由时需要排除
                    if (in_array($row['as'], array('403', '404', 'login', 'logout', 'register', 'password_remind', 'password_getremind', 'password_reset', 'password_getreset'))) {
                        unset($roles_access[$key]['list'][$row_key]);
                        continue;
                    }
                    if (empty($row['as'])) {
                        unset($roles_access[$key]['list'][$row_key]);
                        continue;
                    } else {
                        //2、获取当前角色的所具有的权限
                        $is_role = RolesPermission::where('rid', '=', $rid)->where('name', '=', $row['as'])->first();
                        if (!$is_role) {
                            unset($roles_access[$key]['list'][$row_key]);
                            continue;
                        }
                    }

                    //所有条件满足，生产新输出数组
                    if (isset($row['weight'])) {
                        $new_roles_access[$access['siderbar']]['list'][$row['weight']] = array(
                            'as' => $row['as'],
                            'title' => $row['title'],
                            'description' => $row['description']
                        );
                    }
                }
            }
        }

        return $new_roles_access;
    }

}
