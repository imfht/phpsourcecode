<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 15:44
 * @author 路飞<lf@ourstu.com>
 */

namespace Shop\Widget;

use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Shop@Widget/search'));
    }

    public function assignIssue()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $field = 'sell_num';
            $order = modC('SHOP_SHOW_ORDER_TYPE', 'desc', 'Shop');

            $map = array('status' => 1, 'goods_name' => array('like', '%' . $keywords . '%'));
            $content = D('Shop')->where($map)->order($field . ' ' . $order)->select();

            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
                $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            }
            $data = $content;
            unset($v);
        }

        $this->assign('contents_new', $data);
    }
}