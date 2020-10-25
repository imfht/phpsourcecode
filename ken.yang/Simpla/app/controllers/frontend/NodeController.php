<?php

/*
 * Node内容
 */

class NodeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index($nid) {
        //浏览量增加
        $node = Node::find($nid);
        if (!$node) {
            return Redirect::to('404');
        }
        $node->view = $node->view + 1;
        $node->save();

        //查看是否具有缓存
        if (Cache::has('node-' . $nid)) {
            return Cache::get('node-' . $nid);
        }

        $node = Node::load_all($nid);
        $template = Theme::node($node);

        //设置标题、描述等SEO信息
        list($title, $description, $keywords) = Seo::load_node_seo($node);
        View::share('title', $title);
        View::share('description', $description);
        View::share('keywords', $keywords);
        //获取评论
        $node['comment_code'] = Comment::get($node);

        $node['node_content_top'] = '';
        $node['node_content_bottom'] = '';

        /**
         * hook_node_page_load
         */
        $node = Hook_node::node_page_load($node);

        $result = Setting::checkCache('node');
        //没有缓存的情况下查看内容是否进行了缓存配置
        if ($result) {
            //设置了缓存，则存入缓存中
            $node_view = View::make($template, $node)->render();
            Cache::forever('node-' . $nid, $node_view);
            return $node_view;
        } else {
            return View::make($template, $node);
        }
    }

}
