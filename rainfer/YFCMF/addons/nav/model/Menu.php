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

namespace addons\nav\model;

use app\common\model\Addon;
use think\facade\Lang;

/**
 * 导航模型
 * @Author: rainfer <rainfer520@qq.com>
 */
class Menu extends Addon
{
    /**
     * 获取前台菜单ids
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
    public function get_menu_byid($id = 0, $self = false, $status = 0, $field = 'id', $lang = false)
    {
        $where = [];
        if (empty($status)) {
            $where[] = ['status', '=', 0];
        } elseif ($status == 1) {
            $where[] = ['status', '=', 1];
        }
        if ($lang) {
            $where[] = ['lang', '=', Lang::detect()];
        }
        $arr        = $this->where($where)->column('*','id');
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
            $arr     = $this->where($where)->field($field)->order('sort asc')->select();
            return $arr;
        }
    }
}
