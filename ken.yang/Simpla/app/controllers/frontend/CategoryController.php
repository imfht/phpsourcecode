<?php

/*
 * 分类Category
 */

class CategoryController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index($cid) {
        //查看是否具有缓存
        if (Cache::has('category-' . $cid)) {
            return Cache::get('category-' . $cid);
        }

        //获取该cid分类信息
        $category = Category::find($cid);
        if (!$category) {
            return Redirect::to('404');
        }
        //获取该cid及下面的所有分类
        $categories = Category::get_all_category_by_cid($cid);
        
        $cids = array();
        foreach ($categories as $item) {
            $cids[] = $item['id'];
        }
        //获取该分类下的所有nid
        $limit = Setting::get_list_num('category');
        $nodes_all = Node::Join('category_data', 'category_data.nid', '=', 'node.id')->whereIn('category_data.cid',$cids)->where('node.status', 1)->orderBy('node.sticky','desc')->orderBy('created_at','desc')->paginate($limit);
        $nodes = array();
        foreach ($nodes_all as $key => $node) {
            $nodes[] = Node::load_all($node->nid);
        }
        //获取分页
        $paginate = $nodes_all->links();

        //设置标题、描述等SEO信息
        list($title, $description, $keywords) = Seo::load_category_seo($category);
        View::share('title', $title);
        View::share('description', $description);
        View::share('keywords', $keywords);


        /**
         * hook_category_load
         */
        $category = Hook_category::category_load($category);

        $data = array(
            'category' => $category,
            'nodes' => $nodes,
            'paginate' => $paginate,
            'category_content_top' => '',
            'category_content_bottom' => ''
        );

        /**
         * hook_category_page_load
         */
        $data = Hook_category::category_page_load($data);

        $result = Setting::checkCache('category');
        //没有缓存的情况下查看内容是否进行了缓存配置
        if ($result) {
            //设置了缓存，则存入缓存中
            $template = Theme::category($nodes, $category);
            $category_view = View::make($template, $data)->render();
            Cache::forever('category-' . $cid, $category_view);
            return $category_view;
        } else {
            $template = Theme::category($nodes, $category);
            return View::make($template, $data);
        }
    }

}
