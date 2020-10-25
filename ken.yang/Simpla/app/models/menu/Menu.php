<?php

/*
 * 用户角色表
 */

class Menu extends Eloquent {

    protected $table = 'menu';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('title', 'description', 'url', 'pid', 'tid');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    /**
     * 根据菜单类型ID获取级别菜单
     */
    public static function get_all_menu_by_id($tid) {
        $menus = Menu::where('tid', '=', $tid)->orderBy('pid', 'asc')->orderBy('weight', 'asc')->get()->toArray();

        //组装为按级别排序的数组
        $tree = Base::get_tree($menus);
        return $tree;
    }

    /**
     * 根据菜单类型机器名字获取级别菜单
     */
    public static function get_all_menu_by_name($name) {
        $menus = Menu::where('name', '=', $name)->orderBy('pid', 'asc')->orderBy('weight', 'asc')->get()->toArray();

        //组装为按级别排序的数组
        $tree = Base::get_tree($menus);
        return $tree;
    }

}
