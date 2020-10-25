<?php
/*widget*/

namespace About\Widget;


use Think\Controller;

class NewsBlockWidget extends Controller{
    public function render($category_id = 2)
    {
        $this->assignAbout($category_id);
        $this->display(T('Application://About@Widget/news'));
    }

    private function assignAbout($category_id = 2)
    {
        $num = modC('ABOUT_NEWS_SHOW_COUNT', 4, 'About');
        $field = modC('ABOUT_NEWS_SHOW_ORDER_FIELD', 'sort', 'About');
        $order = modC('ABOUT_NEWS_SHOW_ORDER_TYPE', 'desc', 'About');
        $cache = modC('ABOUT_NEWS_SHOW_CACHE_TIME', 600, 'About');
        $list = S('About_news_home_data');
        if (!$list) {
                $map = array('status' => 1,'category'=> $category_id);
                $list = D('About/About')->getList($map,$field . ' ' . $order,$num);
        }
        S('About_news_home_data', $list, $cache);
        $this->assign('About_lists', $list);
    }
}