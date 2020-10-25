<?php
namespace app\common\api;

use app\common\logic\MupLogic;
use think\Db;
use think\Log;

/**
 * Class RBAC
 * @package app\common\api
 */
class RBAC {
    /**
     * 检查当前操作是否需要认证
     * @return bool
     */
    static function access_need() {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (config('user_auth_on')) {
            // 默认不需要认证模块
            if (in_array(MODULE_NAME, config('not_auth_module'))) {
                return false;
            }

            //  超级管理员不用验证
            if (session('is_admin') == 'Y') {
                return false;
            }

            return true;
        }

        // 不需要认证
        return false;
    }

    /**
     * 是否通过权限认证
     * @param $priv_obj
     * @return bool
     */
    static public function access_pass($priv_obj) {
        //检查是否需要认证
        if (RBAC::access_need()) {
            // 需要认证的
            $user_priv = session('user_priv');
            if (empty($user_priv)) {
                return false;
            }

            $obj = array_column($user_priv, 'priv_obj_id');
            $obj_key = array_search($priv_obj, $obj);
            if ($obj_key === false) {
                // 没有权限访问功能
                return false;
            } else {
                // 判断有没有权限访问动作
                $actions = $user_priv[$obj_key]['actions'];
                $action = ACTION_NAME;
                $action = MupLogic::action_to_char($action);
                switch ($action) {
                    case 'S':
                    case 'U':
                    case 'T':
                        if (strpos($actions, 'B') !== false) {
                            return true;
                        }
                        break;
                }
                if (strpos($actions, $action) === false) {
                    return false;
                }
            }

            return true;
        } else {
            return true;
        }
    }

    /**
     * 取得当前用户的所有权限列表
     * @access public
     */
    static public function access_init() {
        // 用户类型 单独获取权限
        // 其他类型 默认员工、默认客户、 默认供应商 使用统一的默认用户
        $user_id = "";
        switch (session('user_type')) {
            case 'user':
                $user_id = session('user_id');
                break;
            case 'emp':
                $user_id = 'def_emp';
                break;
            case 'eba':
                $user_id = 'def_eba';
                break;
            case 'sup':
                $user_id = 'def_sup';
                break;
        }

        // 管理员只能获得所有的模块及功能对象，获取不到对象的动作
        // 用户直接获取到，所绑定的所有角色的可操作的模块的功能对象，及对象的动作
        $modu_obj = self::get_user_module_priv($user_id);

        if ('Y' == session('is_admin')) {
            $obj_action = self::get_obj_action();  // 所有模块的对象的操作,管理员使用
            $obj_action = array_merge_column($obj_action, 'obj_id', 'action_id');
            $user_priv = array_add_column($modu_obj, $obj_action, 'priv_obj_id', 'obj_id', 'action_id', 'actions');
        } else {
            $user_priv = array_merge_column($modu_obj, 'priv_obj_id', 'actions');
        }

        session('user_priv', $user_priv);

        // 系统的导航菜单
        $sys_nag_group = self::get_nag_group();
        // 用户的功能模块与系统的导航分组，分析能显示的菜单
        //$user_modu = array_column($modu_obj, 'modu_id');
        //$user_modu = array_unique($user_modu);
        //// 重新生成序列索引
        //$user_modu = array_slice($user_modu, 0, count($user_modu));

        //$add_nag = [];
        //for($i = 0, $c = count($user_modu); $i < $c; $i++){
        //    $add_nag[] = Mup::modu_belong_nag($user_modu[$i]);
        //}
        //$add_nag = array_unique($add_nag);
        //$add_nag = array_slice($add_nag, 0, count($add_nag));
        //
        //$user_nag = array_column_equal_arr($sys_nag_group, 'nag_group_id', $add_nag);
        //$user_nag = Mup::modu_add_icon($user_nag);
        //foreach($user_nag as $key => $val){
        //    $user_nag[$key]['have_modu'] = Mup::nag_get_modu($val['nag_group_id']);
        //}
        //session('user_nag', $user_nag);

        return true;
    }

    /**
     * 返回用户操作的模块和对象
     * @param $user_id
     * @return mixed
     */
    public static function get_user_module_priv($user_id) {
        $sql = "";
        if ('Y' == session('is_admin')) {
            $sql = "select distinct modu_id,obj_id as priv_obj_id,obj_name from mup_modu_obj ";
            $result = Db::query($sql);
        } else {
            $sql = "select distinct modu_id,priv_obj_id,obj_name,actions from (SELECT DISTINCT c.modu_id, c.obj_name, b.priv_obj_id, b.actions FROM  mup_user_role a,  mup_role_priv b,  mup_modu_obj c WHERE c.obj_id = b.priv_obj_id AND a.user_id = ? AND a.role_id = b.role_id) a ";
            $result = Db::query($sql, [$user_id]);
        }
        return $result;
    }

    /**
     * 获取导航分组
     */
    public static function get_nag_group() {
        $sql = "select nag_group_id,name,note_info from mup_nag_group";
        return Db::query($sql);
    }

    /**
     * @return mixed
     */
    public static function get_obj_action() {
        $sql = "select action_id, action_name, obj_id from mup_modu_obj_action";
        return Db::query($sql);
    }

    /**
     * 如果session缓存中，没有user_id,就判断为没有登陆
     */
    public static function not_login() {
        if (!session("user_id")) {
            return true;
        } else {
            return false;
        }
    }

}
