<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 11:05
 * @author 路飞<lf@ourstu.com>
 */

namespace Issue\Widget;

use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Issue@Widget/search'));
    }

    public function assignIssue()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $field = modC('ISSUE_SHOW_ORDER_FIELD', 'view_count', 'Issue');
            $order = modC('ISSUE_SHOW_ORDER_TYPE', 'desc', 'Issue');

            $map = array('status' => 1, 'title' => array('like', '%' . $keywords . '%'));
            $content = D('IssueContent')->where($map)->order($field . ' ' . $order)->select();
            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
                $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            }
            $data = $content;

            unset($v);
        }

        $this->assign('IssueContents', $data);
    }
}