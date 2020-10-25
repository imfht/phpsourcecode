<?php
namespace app\common\api;

/**
 * 定义 与 easyui 有关的参数
 * Class Easy
 * @package app\common\api
 */
class Easy {
    /**
     * 列表过虑的字段
     * @param $obj_id
     * @return string
     */
    public static function get_filter_set($obj_id) {
        if (empty($obj_id)) {
            return L('请传递') . ': $obj_id';
        }
        if (!is_array($obj_id)) {
            $obj_id = explode(',', $obj_id);
        }
        $where['obj_id'] = array('in', $obj_id);
        $array = D('Easy/EasySetFilter')->where($where)->order('order_id')->select();
        $fields = array();
        foreach ($array as $val) {
            $fields[] = $val['field'];
        }
        $where['field'] = array('in', $fields);
        $array = D('Easy/EasyTableFields')->where($where)->select();
        return $array;
    }

    /*   获得页面字段
     *   return : array
     **/
    public static function get_table_fields($obj_id) {
        if (!$obj_id) {
            return L('请传递') . ': $obj_id';
        }
        $where['obj_id'] = $obj_id;
        $array = D('Easy/EasyTableFields')->where($where)->select();
        return $array;
    }

}
