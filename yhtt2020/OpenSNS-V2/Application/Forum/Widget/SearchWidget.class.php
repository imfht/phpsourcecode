<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 11:27
 * @author 路飞<lf@ourstu.com>
 */

namespace Forum\Widget;

use Forum\Model\ForumModel;
use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignForumPost();
        $this->display(T('Application://Forum@Widget/search'));
    }

    private function assignForumPost()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $order_key=modC('FORUM_POST_ORDER','last_reply_time', 'Forum');
            $order_type=modC('FORUM_POST_TYPE','desc', 'Forum');

            $map['status']=1;
            $map['title'] = array('like', '%' . $keywords . '%');
            $list = M('ForumPost')->where($map)->order($order_key.' '.$order_type)->select();
            $list = $this->assignForumInfo($list);
        }

        $this->assign('forum_post_list', $list);
    }

    /**关联帖子列表的版块信息
     * @param $list
     * @return mixed
     */
    private function assignForumInfo($list)
    {
        $forumModel = new ForumModel();
        $forum_key_value = $forumModel->getForumKeyValue();
        foreach ($list as &$v) {
            $v['forum'] = $forum_key_value[$v['forum_id']];
        }
        unset($v);
        return $list;
    }
}