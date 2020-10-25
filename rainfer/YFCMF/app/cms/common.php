<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
use app\cms\model\Category as CategoryModel;
use think\facade\Lang;

if (!function_exists('category_text')) {
    /**
     * 返回分类层级text数组
     * @author  rainfer
     *
     * @param string $lang
     * @param bool   $all
     *
     * @return array|mixed
     * @throws
     */
    function category_text($lang = 'zh-cn', $all = false)
    {
        $name      = $all ? 'all' : 'noall';
        $category_text = cache('category_text_' . $name);
        if (empty($category_text)) {
            $map = [];
            if (!config('yfcmf.lang_switch_on')) {
                $map[] = ['lang', '=', $lang];
            }
            $model = new CategoryModel();
            $category_text  = $model->where($map)->order('lang desc,sort')->select();
            $category_text  = tree_left($category_text, 'id', 'pid');
            if ($all == false) {
                $temp = [];
                foreach ($category_text as $value) {
                    $temp[$value['id']] = $value['name'];
                }
                $category_text = $temp;
            }
            cache('category_text_' . $name, $category_text);
        }
        return $category_text;
    }
}
if (!function_exists('get_category_byid')) {
    /**
     * 获取文章分类ids
     * @author rainfer <81818832@qq.com>
     *
     * @param int      $id     待获取的id
     * @param  boolean $self   是否返回自身，默认false
     * @param int      $status 1表示只显示status=1的，0表示只显示status=0的，2表示不限制
     * @param string   $field  默认只返回id数组(一维),其它如:"*"表示全部字段，"id,menu_name"表示返回二维数组
     * @param boolean  $lang   是否只返回当前语言下分类，默认false
     *
     * @return array|mixed
     * @throws \think\Exception
     */
    function get_category_byid($id = 0, $self = false, $status = 0, $field = 'id', $lang = false)
    {
        $where[] = ['id', '=', $id];
        if (empty($status)) {
            $where[] = ['status', '=', 0];
        } elseif ($status == 1) {
            $where[] = ['status', '=', 1];
        }
        if ($lang) {
            $where[] = ['lang', '=', Lang::detect()];
        }
        $model = new CategoryModel();
        $arr        = $model->where($where)->column('*','id');
        if ($arr) {
            $tree = new \Tree();
            $tree->init($arr, ['parentid'=>'pid']);
            $rst = $tree->getChilds($arr, $id, true, true);
        } else {
            $rst = $self ? [$id] : [];
        }
        if (empty($field) || $field == 'id') {
            return $rst;
        } else {
            $where   = [];
            $where[] = ['id', 'in', $rst];
            $arr     = $model->where($where)->field($field)->order('sort asc')->select();
            return $arr;
        }
    }
}
