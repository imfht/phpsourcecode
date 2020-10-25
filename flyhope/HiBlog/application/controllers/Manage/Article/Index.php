<?php

/**
 * 文章列表页
 *
 * @package Controller
 * @author  chengxuan <i@chengxuan.li>
 */
class Manage_Article_IndexController extends AbsController {
    
    /**
     * 默认每页条数
     * 
     * @var int
     */
    protected $_limit = 10;

    
    public function indexAction() {
        $last_page = Comm\Arg::get('last_page', FILTER_VALIDATE_INT, ['min_range' => 1]) ?: 0;
        $page = Comm\Arg::get('p', FILTER_VALIDATE_INT, ['min_range' => 1]) ?: 1;
        $uid = Yaf_Registry::get('current_uid');
        
        //获取总数
        $total = Model\Counter\Article::get(0, $uid);
        
        //获取用户的博客配置中的分页设置
        $blog = Model\Blog::show();
        empty($blog['data']['page_count']) || $this->_limit = $blog['data']['page_count'];
        
        //获取分页参数
        $pager = new \Comm\Pager($total, $this->_limit);
        
        //获取数据
        $articles = Model\Article::showUserList($pager);
        
        //获取分类内容
        $categorys = Model\Category::showUserAll();
        $categorys = Comm\Arr::hashmap($categorys, 'id');
        
        //获取用户博客基本地址
        $blog_url = 'http://' . \Model\Github::showDefaultBlogRepoName();
        
        $this->viewDisplay(array(
            'articles'  => $articles,
            'categorys' => $categorys,
            'pager'     => $pager,
            'blog_url'  => $blog_url,
        ));
    }
    
} 
