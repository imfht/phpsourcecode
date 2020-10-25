<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:14
 */

namespace Forum\Model;

use Think\Model;

class ForumModel extends Model
{
    protected $_validate = array(
        array('title', '1,99999', '标题不能为空', self::EXISTS_VALIDATE, 'length'),
        array('title', '0,100', '标题太长', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('post_count', '0', self::MODEL_INSERT),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
    );

    public function getAllForumsSortByTypes()
    {
        $forum_type = D("ForumType")->where(array('status' => 1))->order('sort desc')->select();
        foreach ($forum_type as &$t) {
            $t['forums'] = $this->getForumList(array('status' => 1, 'type_id' => $t['id']));
        }

        return $forum_type;
    }

    public  function getForumList($map_type = array('status' => 1))
    {
        $tag='forum_list_'.serialize($map_type);
        $forum_list = S($tag);
        $cache_time=modC('CACHE_TIME',300,'Forum');
        if (empty($forum_list)) {
            //读取板块列表

            $forum_list = D('Forum/Forum')->where($map_type)->order('sort asc')->select();
            $forumPostModel = D('ForumPost');
            $forumPostReplyModel = D('ForumPostReply');
            $forumLzlReplyModel = D('ForumLzlReply');
            foreach ($forum_list as &$f) {
                $map['status'] = 1;
                $map['forum_id']=$f['id'];
                $f['background'] = $f['background'] ? getThumbImageById($f['background'], 800, 'auto') : C('TMPL_PARSE_STRING.__IMG__') . '/default_bg.jpg';
                $f['logo'] = $f['logo'] ? getThumbImageById($f['logo'], 128, 128) : C('TMPL_PARSE_STRING.__IMG__') . '/default_logo.png';
                $f['topic_count'] = $forumPostModel->where($map)->count();
                $post_id=$forumPostModel->where(array('forum_id'=>$f['id']))->field('id')->select();
                $p_id=getSubByKey($post_id,'id');
                $map['post_id']=array('in',implode(',',$p_id));
                $f['total_count'] = $f['topic_count'] + $forumPostReplyModel->where($map)->count();// + $forumLzlReplyModel->where($map)->count();


            }
            unset($f);
            S($tag, $forum_list, $cache_time);
        }
        return $forum_list;
    }
}
