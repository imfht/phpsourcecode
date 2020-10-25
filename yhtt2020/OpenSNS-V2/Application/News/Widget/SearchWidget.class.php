<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 11:34
 * @author 路飞<lf@ourstu.com>
 */

namespace News\Widget;

use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignNews();
        import_lang('News');
        $this->display(T('Application://News@Widget/search'));
    }

    private function assignNews()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $num = 999;
            $field = modC('NEWS_SHOW_ORDER_FIELD', 'view', 'News');
            $order = modC('NEWS_SHOW_ORDER_TYPE', 'desc', 'News');

            $map = array('status' => 1, 'dead_line' => array('gt', time()), 'title' => array('like', '%' . $keywords . '%'));
            $list = D('News/News')->getList($map, $field . ' ' . $order, $num);

            foreach ($list as &$v) {
                $v['user'] = query_user(array('space_url', 'nickname'), $v['uid']);
            }
            unset($v);
            if (!$list) {
                $list = 1;
            }
            unset($v);
            if ($list == 1) {
                $list = null;
            }
        }

        $this->assign('news_lists', $list);
    }
}