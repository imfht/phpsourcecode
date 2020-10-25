<?php
/**
 * 所属项目 OpenSNS开源免费版.
 * 开发者: 陈一枭
 * 创建日期: 2015-03-27
 * 创建时间: 15:48
 * 版权所有 想天软件工作室(www.ourstu.com)
 */
namespace Issue\Widget;

use Think\Controller;

class HomeBlockWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Issue@Widget/homeblock'));
    }

    public function assignIssue()
    {
        $num = modC('ISSUE_SHOW_COUNT', 4, 'Issue');
        $field = modC('ISSUE_SHOW_ORDER_FIELD', 'view_count', 'Issue');
        $order = modC('ISSUE_SHOW_ORDER_TYPE', 'desc', 'Issue');
        $cache = modC('ISSUE_SHOW_CACHE_TIME', 600, 'Issue');
        $data = S('issue_home_data');
        if (empty($data)) {
            $map = array('status' => 1);
            $content = D('IssueContent')->where($map)->order($field . ' ' . $order)->limit($num)->select();
            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
                $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            }
            $data = $content;
            S('issue_home_data', $data, $cache);
        }
        unset($v);
        $this->assign('IssueContents', $data);
    }
} 