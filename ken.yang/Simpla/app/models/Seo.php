<?php

/*
 * 区块表
 */

class Seo extends Eloquent {

    protected $table = 'seo';
    //fillable 属性指定哪些属性可以被集体赋值。这可以在类或接口层设置。
    //fillable 的反义词是 guarded，将做为一个黑名单而不是白名单：
    protected $fillable = array('type', 'tid', 'nid', 'title', 'description', 'key');
    //注意在默认情况下您将需要在表中定义 updated_at 和 created_at 字段。
    //如果您不希望这些列被自动维护，在模型中设置 $timestamps 属性为 false。
    public $timestamps = true;

    /**
     * 获取首页SEO
     * @return array
     */
    public static function load_home_seo() {
        $home_seo = self::where('type', '=', 'home')->get()->toArray();
        $title = strip_tags($home_seo[0]['title']);
        $description = strip_tags($home_seo[0]['description']);
        $keywords = strip_tags($home_seo[0]['keywords']);

        return array($title, $description, $keywords);
    }

    /**
     * 获取node的seo
     * @param array $node
     * @return array
     */
    public static function load_node_seo($node) {
        $node_seo = self::where('nid', '=', $node['node']['id'])->get()->toArray();
        if ($node_seo) {
            $title = strip_tags($node_seo[0]['title']);
            $description = strip_tags($node_seo[0]['description']);
            $keywords = strip_tags($node_seo[0]['keywords']);
        } else {
            $title = strip_tags($node['title']);
            $description = '';
            $keywords = '';
        }

        return array($title, $description, $keywords);
    }

    public static function load_node_edit_seo($node) {
        $node_seo = self::where('nid', '=', $node['id'])->get()->toArray();
        if ($node_seo) {
            $title = strip_tags($node_seo[0]['title']);
            $description = strip_tags($node_seo[0]['description']);
            $keywords = strip_tags($node_seo[0]['keywords']);
        } else {
            $title = '';
            $description = '';
            $keywords = '';
        }

        return array($title, $description, $keywords);
    }

    /**
     * 获取分类的seo
     * @param array $category
     * @return array
     */
    public static function load_category_seo($category) {
        $category_seo = self::where('cid', '=', $category['id'])->first();
        if ($category_seo) {
            $title = $category_seo['title'];
            $description = $category_seo['description'];
            $keywords = $category_seo['keywords'];
        } else {
            $title = $category['title'];
            $description = $category['description'];
            $keywords = '';
        }

        return array($title, $description, $keywords);
    }

}
