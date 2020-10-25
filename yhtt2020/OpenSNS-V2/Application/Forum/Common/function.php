<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-6-5
 * Time: 上午10:38
 * @author 郑钟良<zzl@ourstu.com>
 */


/**
 * 获取要排除的uids(版主、自己)
 * @param int $lzl_reply_id
 * @param int $reply_id
 * @param int $post_id
 * @param int $forum_id
 * @param int $with_self 是否包含记录的uid
 * @return array|int|mixed
 * @author 郑钟良<zzl@ourstu.com>
 */
function get_expect_ids($lzl_reply_id=0,$reply_id=0,$post_id=0,$forum_id=0,$with_self=1)
{
    $uid=0;
    if(!$forum_id){
        if(!$post_id){
            if(!$reply_id){
                $lzl_reply=D('ForumLzlReply')->find($lzl_reply_id);
                $uid=$lzl_reply['uid'];
                $post_id=$lzl_reply['post_id'];
            }else{
                $reply = D('ForumPostReply')->find(intval($reply_id));
                $uid=$reply['uid'];
                $post_id=$reply['post_id'];
            }
        }
        $post=D('ForumPost')->where(array('id' => $post_id, 'status' => 1))->find();
        $forum_id=$post['forum_id'];
        if(!$uid){
            $uid=$post['uid'];
        }
    }
    $forum=D('Forum')->find($forum_id);
    if(mb_strlen($forum['admin'],'utf-8')){
        $expect_ids=str_replace('[','',$forum['admin']);
        $expect_ids=str_replace(']','',$expect_ids);
        $expect_ids=explode(',',$expect_ids);
        if($uid&&$with_self){
            if(!in_array($uid,$expect_ids)){
                $expect_ids=array_merge($expect_ids,array($uid));
            }
        }
    }else{
        if($with_self&&$uid){
            $expect_ids=$uid;
        }else{
            $expect_ids=-1;
        }
    }
    return $expect_ids;
}

/**
 * 论坛板块是否允许发帖
 * @param $forum_id
 * @return bool
 * @author 郑钟良<zzl@ourstu.com>
 */
function forumAllowCurrentUserGroup($forum_id)
{
    $forum_id = intval($forum_id);
    //如果是超级管理员，直接允许
    if (is_login() == 1) {
        return true;
    }

    //如果帖子不属于任何板块，则允许发帖
    if (intval($forum_id) == 0) {
        return true;
    }

    //读取论坛的基本信息
    $forum = D('Forum')->where(array('id' => $forum_id))->find();
    $userGroups = explode(',', $forum['allow_user_group']);

    //读取用户所在的权限组
    $list = M('AuthGroupAccess')->where(array('uid' => is_login()))->select();
    foreach ($list as &$e) {
        $e = $e['group_id'];
    }


    //判断权限组是否有权限
    $list = array_intersect($list, $userGroups);
    return $list ? true : false;
}