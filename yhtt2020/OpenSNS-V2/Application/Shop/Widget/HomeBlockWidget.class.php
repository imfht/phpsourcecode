<?php
/**
 * 所属项目 OpenSNS开源免费版.
 * 开发者: 陈一枭
 * 创建日期: 2015-03-27
 * 创建时间: 15:48
 * 版权所有 想天软件工作室(www.ourstu.com)
 */
namespace Shop\Widget;

use Think\Controller;

class HomeBlockWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Shop@Widget/homeblock'));
    }

    public function assignIssue()
    {
        $num = modC('SHOP_SHOW_COUNT', 4, 'Shop');
        $field = modC('SHOP_SHOW_ORDER_FIELD', 'view_count', 'Shop');
        $order = modC('SHOP_SHOW_ORDER_TYPE', 'desc', 'Shop');
        $cache = modC('SHOP_SHOW_CACHE_TIME', 600, 'Shop');
        $show_type=modC('SHOP_SHOW_TYPE',1,'Shop');


        $data = S('Shop_home_data');
        if (empty($data)) {
            $map = array('status' => 1);
            if($show_type==1){
                $map['is_new']=$show_type;
            }

            $content = D('Shop')->where($map)->order($field . ' ' . $order)->limit($num)->select();

            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
                $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            }
            $data = $content;
            S('Shop_home_data', $data, $cache);
        }
        unset($v);

        $this->assign('contents_new', $data);
    }
} 