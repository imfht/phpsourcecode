<?php
namespace Scoreshop\Widget;

use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Scoreshop@Widget/search'));
    }

    public function assignIssue()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $field = 'sell_num';
            $order = modC('SCORESHOP_SHOW_ORDER_TYPE', 'desc', 'Scoreshop');

            $map = array('status' => 1, 'goods_name' => array('like', '%' . $keywords . '%'));
            $content = D('Scorehop')->where($map)->order($field . ' ' . $order)->select();

            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
            }
            $data = $content;
            unset($v);
        }

        $this->assign('contents_new', $data);
    }
}