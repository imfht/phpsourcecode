<?php

class SiteController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function index() {
        //1、查看是否具有缓存
        if (Cache::has('home')) {
            return Cache::get('home');
        }
        //设置标题、描述等SEO信息
        list($title, $description, $keywords) = Seo::load_home_seo();
        View::share('title', $title);
        View::share('description', $description);
        View::share('keywords', $keywords);

        $limit = Setting::get_list_num('home');
        $nodes_all = Node::where('status', '=', 1)->where('promote', '=', 1)->orderBy('created_at', 'desc')->paginate($limit);
        $nodes = array();
        foreach ($nodes_all as $node) {
            $nodes[] = Node::load_all($node->id);
        }
        //获取分页
        $paginate = $nodes_all->links();

        $data = array(
            'nodes' => $nodes,
            'paginate' => $paginate,
            'home_content_top' => '',
            'home_content_bottom' => ''
        );
        /**
         * hook_home_page_load
         */
        $data = Hook_page::home_page_load($data);

        $result = Setting::checkCache('home');
        //没有缓存的情况下查看内容是否进行了缓存配置
        if ($result) {
            //设置了缓存，则存入缓存中
            $template = Theme::template('home');
            $home_view = View::make($template, $data)->render();
            Cache::forever('home', $home_view);
            return $home_view;
        } else {
            $template = Theme::template('home');
            return View::make($template, $data);
        }
    }

    /**
     * 403错误
     */
    public function message_403() {
        //设置标题、描述等SEO信息
        View::share('title', '403');
        View::share('description', '没有权限访问');

        $template = Theme::template('403');
        return View::make($template);
    }

    /**
     * 404错误
     */
    public function message_404() {
        //设置标题、描述等SEO信息
        View::share('title', '404');
        View::share('description', '没有找到该页面');

        $template = Theme::template('404');
        return View::make($template);
    }

    /**
     * sitemap站点地图
     */
    public function sitemap() {
        //设置标题、描述等SEO信息
        View::share('title', '站点地图');
        View::share('description', '站点地图');

        $template = Theme::template('sitemap');
        return View::make($template);
    }

    /**
     * link友情链接
     */
    public function link() {
        //设置标题、描述等SEO信息
        View::share('title', '友情链接');
        View::share('description', '友情链接');

        $template = Theme::template('link');
        return View::make($template);
    }

}

?>