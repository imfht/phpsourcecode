<?php
namespace app\common\api;

use think\Config;

/**
 * Class Menu
 * @package app\common\api
 */
class Menu {

    /**
     * 返回导航风格界面
     * @param $frame_id // 想获取的界面风格
     * @return array
     */
    public static function get_menu_data($frame_id = null) {
        $nag_menu_data = session('nag_menu_data');
        if(!empty($nag_menu_data)){
            return $nag_menu_data;
        }
        $user_frame_id = Frame::user_frame_id();
        // 从默认配置中获取各分组的功能
        $frame_nag = Config::parse(APP_PATH . 'extra/oit_frame_nag_group_func.json');
        if (empty($user_frame_id)) {
            // 用户没有绑定的界面风格，就根据默认的 软件版本加载风格
            // 删除 没有权限的功能,停用的单据
            // 员工 只是拥有 def_emp 默认员工用户的权限,但是不拥有 def_emp的导航分组
            // 真实员工、客户、供应商，都只显示有权限的功能
            $sys_frame_id = 'frame_' . Para::system_para_get('soft_product_id');
            // 获取此风格的导航分组
            $sys_frame_nag = Frame::frame_nag($sys_frame_id, ['nag_group_id', 'order_id']);
            // 查询系统的导航分组
            $nag_info = Frame::nag_group(null, ['nag_group_id', 'name']);
            // 导航分组与功能合并
            foreach ($sys_frame_nag as $key => $val) {
                foreach ($nag_info as $k => $v) {
                    if ($v['nag_group_id'] == $val['nag_group_id']) {
                        $sys_frame_nag[$key]['name'] = $v['name'];
                        break;
                    }
                }
                $sys_frame_nag[$key]['item'] = $frame_nag[$val['nag_group_id']];
            }

            $result_frame_nag = $sys_frame_nag;

            // 去掉不需要的数据
            // 1 权限中没有priv_obj_id的
            // 2 停用的单据
            // 用户有权限访问的功能
            $user_priv = session('user_priv');
            $objs = array_column($user_priv, 'priv_obj_id');
            $is_admin = session('is_admin');
            $stop_objs = '';

            foreach ($result_frame_nag as $key => $val) {
                foreach ($val['item'] as $k_i => $v_i) {
                    // 管理员才能访问的
                    if (isset($v_i['user_type'])) {
                        if ($is_admin === 'Y' && $v_i['user_type'] == 'admin') {
                            continue;
                        }
                    }
                    // 有权限访问的 - 如果是管理员，权限都会拥有
                    if (isset($v_i['priv_obj'])) {
                        if (in_array($v_i['priv_obj'], $objs)) {
                            continue;
                        }
                    }
                    // todo::停用的单据删除
                    // 其他的都删除
                    unset($result_frame_nag[$key]['item'][$k_i]);
                }
                if (count($result_frame_nag[$key]['item']) == 0) {
                    unset($result_frame_nag[$key]);
                }
            }
            $result_frame_nag = array_merge($result_frame_nag, []);
        } else {
            // 用户保存了自定义界面风格
            // 就不再去除没有权限的功能对象，而在具体操作时再判断有没有权限执行
            // 获取绑定的界面分组名称 - 自定义的，可能与系统不一致
            $user_frame_nag = Frame::user_nag_group($user_frame_id);
            // 获取自定义的分组对应的功能
            $user_frame_nag_func = Frame::user_nag_group_item($user_frame_id);
            foreach ($user_frame_nag as $k => $v) {
                foreach ($user_frame_nag_func as $key => $val) {
                    // 加入至导航分组中
                    if ($v['nag_group_id'] == $val['nag_group_id']) {
                        $user_frame_nag[$k]['item'][] = $val;
                    }
                }
            }

            //并加载导航分组的对应入口功能 Func
            $result_frame_nag = $user_frame_nag;
        }
        session('nag_menu_data', $result_frame_nag);
        return $result_frame_nag;
    }

}
