<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-6-8
 * Time: 下午4:37
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Forum\Widget;


use Forum\Model\ForumModel;
use Think\Controller;

class HomeBlockWidget extends Controller{
    public function render()
    {
        $this->assignForum();
        $this->assignForumPost();
        $this->display(T('Application://Forum@Widget/homeblock'));
    }

    private function assignForum()
    {
        $data = S('FORUM_SHOW_DATA');
        $forumModel = new ForumModel();
        if (empty($data)) {
            $forum_ids = modC('FORUM_SHOW', '', 'Forum');
            $cache_time = modC('FORUM_SHOW_CACHE_TIME', 600, 'Forum');
            $forum_ids=explode('|', $forum_ids);
            $forum= $forumModel->where(array('status' => 1,'id' => array('in',$forum_ids)))->select();
            $forum=array_combine(array_column($forum,'id'),$forum);
            $data=array();
            foreach($forum_ids as $val){
                if($val!=''&&$forum[$val]){
                    $data[]=$forum[$val];
                }
            }
            if(!count($data)){
                $data=1;
            }
            S('FORUM_SHOW_DATA', $data,$cache_time);
        }
        if($data==1){
            $data=null;
        }
        foreach ($data as &$v) {
            $v['hasFollowed'] = $forumModel->checkFollowed($v['id'], is_login());
        }
        unset($v);
        $this->assign('forum_show', $data);
    }

    private function assignForumPost()
    {
        $list = S('FORUM_POST_SHOW_DATA');
        if (empty($list)) {
            $order_key=modC('FORUM_POST_ORDER','last_reply_time', 'Forum');
            $order_type=modC('FORUM_POST_TYPE','desc', 'Forum');
            $limit=modC('FORUM_POST_SHOW_NUM',5, 'Forum');
            $cache_time = modC('FORUM_POST_CACHE_TIME', 600, 'Forum');

            $map['status']=1;
            $list = M('ForumPost')->where($map)->order($order_key.' '.$order_type)->limit($limit)->select();
            $list = $this->assignForumInfo($list);
            S('FORUM_POST_SHOW_DATA', $list,$cache_time);
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