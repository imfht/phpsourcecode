<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-7-21
 * Time: 上午11:25
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Forum\Model;

use Think\Model;

class ForumProtocolModel extends Model
{

    private $forum_postModel;
    private $forumModel;
    private $forum_replyModel;
    private $forum_lzl_replyModel;

    public function _initialize()
    {
        $this->forum_postModel = new Model('ForumPost');
        $this->forumModel = new Model('Forum');
        $this->forum_replyModel = new Model('ForumPostReply');
        $this->forum_lzl_replyModel = new Model('ForumLzlReply');
    }

    // 在个人空间里查看该应用的内容列表
    public function profileContent($uid = null, $page = 1, $count = 15, $tab = null)
    {
        $tab = $tab ? $tab : 'forum';
        $forums = $this->_getForumList();
        $forum_key_value = array();
        foreach ($forums as $f) {
            $forum_key_value[$f['id']] = $f;
        }
        if ($uid != 0) {
            $map['uid'] = $uid;
        } else {
            $map['uid'] = is_login();
        }
        if ($tab == 'forum') {
            $map['status'] = 1;
            $result = $this->forum_postModel->where($map)->page($page, $count)->order('update_time desc')->select();
            foreach ($result as &$v) {
                $v['forum'] = $forum_key_value[$v['forum_id']];
            }
        } elseif ($tab == 'forum_in') {
            $map_in = $this->_getInMap($map);
            unset($map_in['uid']);
            $map_in['status'] = 1;
            $result = $this->forum_postModel->where($map_in)->page($page, $count)->order('update_time desc')->select();
            foreach ($result as &$v) {
                $v['forum'] = $forum_key_value[$v['forum_id']];
            }
        }
        $view = new \Think\View();
        $view->assign('list', $result);
        $view->assign('tab', $tab);
        $view->assign('uid', $uid);
        $view->assign('type', 'forum');
        $content = '';
        $content = $view->fetch(T('Application://Forum@Index/profile_content'), $content);
        return $content;
    }

    //返回列表项总数，分页用
    public function getTotalCount($uid = null, $tab = 'forum')
    {
        $tab = $tab ? $tab : 'forum';
        if ($uid != 0) {
            $map['uid'] = $uid;
        } else {
            $map['uid'] = is_login();
        }
        if ($tab == 'forum') {
            $map['status'] = 1;
            $totalCount = $this->forum_postModel->where($map)->count();
        } elseif ($tab == 'forum_in') {
            $map_in = $this->_getInMap($map);
            $map_in['status'] = 1;
            $totalCount = $this->forum_postModel->where($map_in)->count();
        }
        return $totalCount;
    }

    //返回中文名称
    public function getModelInfo()
    {
        return array('title' => "论坛", 'sort' => 90);
    }


    private function _getForumList()
    {
        $forum_list = S('forum_list');
        if (empty($forum_list)) {
            //读取板块列表
            $forum_list = D('Forum/Forum')->where(array('status' => 1))->order('sort asc')->select();
            S('forum_list', $forum_list, 300);
        }
        return $forum_list;
    }

    /**我参与的$map
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _getInMap($map = array())
    {
        $map_reply = $map;
        $map_reply['status'] = 1;
        $reply_ids = $this->forum_replyModel->where($map_reply)->field('post_id')->select();
        $reply_ids = array_column($reply_ids, 'post_id');
        $map_lzl_reply = $map;
        $map_lzl_reply['is_del'] = 0;
        $lzl_reply_ids = $this->forum_lzl_replyModel->where($map_lzl_reply)->field('post_id')->select();
        $lzl_reply_ids = array_column($lzl_reply_ids, 'post_id');
        $in_ids = array_unique(array_merge($reply_ids, $lzl_reply_ids));
        $map['id'] = array('in', $in_ids);

        return $map;
    }
}