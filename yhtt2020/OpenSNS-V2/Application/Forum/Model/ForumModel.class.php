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
        $forum_type = D("ForumType")->where(array('status' => 1))->order('sort asc')->select();
        foreach ($forum_type as &$t) {
            $t['forums'] = $this->getForumList(array('status' => 1, 'type_id' => $t['id']));
        }

        return $forum_type;
    }
    public function cleanAllForumsCache(){

        $forum_type = D("ForumType")->where(array('status' => 1))->order('sort asc')->select();
        foreach ($forum_type as &$t) {
            $this->cleanCache(array('status' => 1, 'type_id' => $t['id']));
        }

    }
    public function cleanCache($map_type=array('status'=>1)){
        $tag = 'forum_list_' . serialize($map_type);
        S($tag,null);
    }

    public function getForumList($map_type = array('status' => 1))
    {

        $tag = 'forum_list_' . serialize($map_type);
        $forum_list = S($tag);
        $cache_time = modC('CACHE_TIME', 300, 'Forum');

        if (empty($forum_list)) {
            //读取板块列表

            $forum_list = D('Forum/Forum')->where($map_type)->order('sort asc')->select();
            $forumPostModel = D('ForumPost');
            $forumPostReplyModel = D('ForumPostReply');

            foreach ($forum_list as &$f) {
                $map['status'] = 1;
                $map['forum_id'] = $f['id'];
                $f['background'] = intval($f['background']) != 0 ? getThumbImageById($f['background'], 980, 180) : C('TMPL_PARSE_STRING.__IMG__') . '/default_head.jpg';
                $f['logo'] = intval($f['logo']) != 0 ? getThumbImageById($f['logo'], 128, 128) : C('TMPL_PARSE_STRING.__IMG__') . '/default_logo.jpg';
                $f['topic_count'] = $forumPostModel->where($map)->count();
                $f['admin'] = explode(',', str_replace('[', '', str_replace(']', '', $f['admin'])));
                $post_id = $forumPostModel->where(array('forum_id' => $f['id']))->field('id')->select();
                $p_id = getSubByKey($post_id, 'id');
                $map['post_id'] = array('in', implode(',', $p_id));
                $f['total_count'] = $f['topic_count'] + $forumPostReplyModel->where($map)->count();
            }
            unset($f);
            S($tag, $forum_list, $cache_time);
        }

        foreach ($forum_list as &$f) {
            if (count($f['admin']) > 0) {
                foreach ($f['admin'] as $a) {
                    if ($a != '')
                        $f['admins'][] = query_user(array('nickname', 'space_link'), $a);
                }
            }
        }
        unset($f);
return $forum_list;
}

    /**获得版块的键值对
     * @return mixed
     */
    public function getForumKeyValue()
    {
        $forums = $this->getForumList();
        $forum_key_value = array();
        foreach ($forums as $f) {
            $forum_key_value[$f['id']] = $f;
        }
        return $forum_key_value;

    }




    /**关注版块
     * @param int $id 版块ID
     * @param int $uid 用户ID，默认为登陆帐户
     * @return bool
     */
    public function following($id = 0, $uid = 0)
    {

        if (empty($id)) {
            $this->error = L('_ID_IS_NOT_LEGAL_WITH_PERIOD_');
            return false;
        }
        $data['forum_id'] = $id;
        $data['uid'] = empty($uid) ? is_login() : $uid;
        if ($data['uid'] == 0) {
            $this->error = L('_UID_ERROR_WITH_PERIOD_');
            return false;
        }
        $had = M('ForumFollow')->where($data)->find();
        if ($had) {
            $rs = M('ForumFollow')->delete($had['id']);
            $follow = 0;
        } else {
            $rs = M('ForumFollow')->add($data);
            $follow = 1;
        }
        if ($rs === false) {
            $this->error = L('_DATABASE_WRITE_ERROR_WITH_PERIOD_');
            return false;
        } else {
            return array(true, $follow);
        }
    }

    /**获取关注的版块列表
     * @param $uid
     * @return array
     */
    public function getFollowForums($uid)
    {
        $forum_ids = M('ForumFollow')->field('forum_id')->where(array('uid' => $uid))->select();
        foreach ($forum_ids as $f_id) {
            $forum[] = $this->find($f_id['forum_id']);
        }

        return $forum;
    }

    public function checkFollowed($id = 0, $uid)
    {
        return M('ForumFollow')->where(array('forum_id' => $id, 'uid' => $uid))->count();
    }
}
