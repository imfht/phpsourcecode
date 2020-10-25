<?php
namespace app\common\api;
// 表格

use app\common\model\app\AppGridFmt;
use app\common\model\app\AppGridFmtDef;
use app\common\model\app\AppGridFmtUser;

/**
 * Class Grid
 * @package app\common\api
 */
class Grid {
    /**
     * 显示方案,获得表格右键明细定义的显示方案
     * @param null   $fmt_id
     * @param int    $method
     * @param        $model
     * @return array|mixed
     */
    public static function get_grid_fmt_def($model = null, $fmt_id = null, $method = 1) {
        // 如果有传递显示方案，直接按显示方案来显示;
        if($fmt_id == null){
            if($model == null){
                return lang('请传递' . '$model 或 $fmt_id');
            }
            $mod = new $model();
            // 1 检查用户有没有绑定方案
            // 2 模型默认显示方案
            if(property_exists($mod, 'owner_fmt_id')){
                $fmt_id = self::get_grid_user_fmt_id($mod->owner_fmt_id);
                if($fmt_id == null){
                    $fmt_id = self::get_grid_default_fmt_id($mod->owner_fmt_id);
                }
            }
        }

        $fmt_list = self::app_grid_fmt_def($fmt_id, $method);
        // 模型中定义的显示方案
        if (!is_array($fmt_list)) {
            $fmt_list = $mod->fmt_field_list;
        }
        if(empty($fmt_list) || !is_array($fmt_list)){
            return lang('未定义' . '$model 显示方案');
        }
        // 去除宽度为 0 的列
        foreach ($fmt_list as $key => $val) {
            if ($val['width'] == 0) {
                unset($fmt_list[$key]);
            }
        }
        array_splice($fmt_list, 0, 0);
        return $fmt_list;
    }

    /**
     * 获得默认的显示方案
     * @param $owner_fmt_id
     * @return bool|mixed
     */
    private static function get_grid_default_fmt_id($owner_fmt_id = null) {
        $mod = new AppGridFmt();
        if($owner_fmt_id == null){
            return false;
        }
        $where['is_default'] = 'Y';
        $where['owner_fmt_id'] = $owner_fmt_id;
        $fmt_id = $mod->where($where)->value('fmt_id');
        return $fmt_id;
    }

    /**
     * 得到用户 绑定  显示方案
     * @param      $owner_fmt_id
     * @param null $user_id
     * @return bool|mixed
     */
    private static function get_grid_user_fmt_id($owner_fmt_id, $user_id = null) {
        if ($owner_fmt_id == null) {
            return false;
        }
        if ($user_id == null) {
            $user_id = session('user_id');
        }
        $where['user_id'] = $user_id;
        $where['owner_fmt_id'] = $owner_fmt_id;
        $mod = new AppGridFmtUser();
        $fmt_id = $mod->where($where)->value('fmt_id');
        return $fmt_id;
    }

    /**
     * 获得默认的显示方案列表的显示项目
     * @param     $fmt_id
     * @param int $show
     * @return array
     */
    public static function app_grid_fmt_def($fmt_id = null, $show = 1) {
        $field = ['col_id','def_width','title', 'order_id'];
        $where['fmt_id'] = $fmt_id;
        if($fmt_id == null){
            return lang('请传递' . '$def_fmt_id');
        }
        // 2 显示所有列 1 显示的列 0 隐藏的列
        if ($show != 2) {
            $where['visible'] = $show;
        }
        $order = 'order_id';
        $app_grid_fmt_def = new AppGridFmtDef();
        $list = $app_grid_fmt_def->where($where)->field($field)->order($order)->select()->toArray();
        // 转换成easyui表格默认的返回列名
        $return_list = [];
        foreach ($list as $key => $value) {
            $return_list[] = ['field' => $value['col_id'], 'width' => intval($value['def_width']), 'title' => $value['title']];
        }
        return $return_list;
    }
}

