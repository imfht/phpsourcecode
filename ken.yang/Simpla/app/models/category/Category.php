<?php

/*
 * 用户角色表
 */

class Category extends Eloquent {

    protected $table = 'category';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('title', 'description', 'pid', 'tid');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = false;

    /**
     * 根据分类类型获取级别分类
     */
    public static function get_all_category($ctid) {
        //header("Content-type: text/html; charset=utf-8");
        $items = Category::where('tid', '=', $ctid)->orderBy('pid', 'asc')->orderBy('weight', 'asc')->get()->toArray();

        //组装为按级别排序的数组
        $tree = Base::get_tree($items);
        return $tree;
    }

    /**
     * 根据分类ID获取所有相关的子分类ID
     */
    public static function get_all_category_by_cid($cid) {
        $categories = Category::where('pid', '=', $cid)->get()->toArray();
        $categories[]['id'] = $cid;
        return $categories;
    }

}
