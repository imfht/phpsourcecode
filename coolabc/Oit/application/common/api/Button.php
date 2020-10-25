<?php
namespace app\common\api;

use app\common\logic\MupLogic;
use think\Config;

class Button {

    /**
     * 功能可操作的动作权限
     * @param $priv_obj
     * @return array
     */
    public static function get_toolbar_access($priv_obj) {
        // 功能按钮组 - 会有按钮的字母
        $objs_def_buttons = 'BCDEFGHUST';

        // 组织页面中的操作按钮
        $actions = [];
        $temp = [];
        $stu_or_b = 0; // 1 分开单独的新增、编辑、删除 2 综合的增删改
        $user_priv = session('user_priv');
        foreach ($user_priv as $key => $val) {
            if ($val['priv_obj_id'] === $priv_obj) {
                $obj_actions = $val['actions'];  // 权限的功能按钮组
                $sys_actions = MupLogic::system_actions();  // 系统的功能按钮组
                foreach($sys_actions as $k => $v){
                    // 在按钮组 并 在可操作的权限动作里
                    if (strpos($objs_def_buttons, $k) !== false && strpos($obj_actions, $k) !== false) {
                        switch ($k) {
                            //单独的新增、编辑、删除
                            case 'S':
                            case 'T':
                            case 'U':
                                if ($stu_or_b != 2) {
                                    $temp['text'] = lang($v);
                                    $temp['id'] = $v;
                                    $temp['icon'] = $v;
                                    $actions[] = $temp;
                                    $stu_or_b = 1;
                                }
                                break;
                            //综合修改权限
                            case 'B':
                                if ($stu_or_b != 1) {
                                    $b_array = explode(',', $v);
                                    foreach ($b_array as $b_val) {
                                        $temp['text'] = lang($b_val);
                                        $temp['id'] = $b_val;
                                        $temp['icon'] = $b_val;
                                        $actions[] = $temp;
                                    }
                                    $stu_or_b = 2;
                                }
                                break;
                            //其他权限
                            default:
                                $temp['text'] = lang($v);
                                $temp['id'] = $v;
                                $temp['icon'] = $v;
                                $actions[] = $temp;
                                break;
                        }
                    }
                }
                break;
            }
        }
        return $actions;
    }


}
