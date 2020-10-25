<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:14
 */

namespace Group\Model;

use Think\Model;

class GroupPostReplyModel extends Model
{
    protected $_validate = array(
        array('content', '1,40000', '内容长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME),
        array('status', '1', self::MODEL_INSERT),
    );

    public function addReply($post_id, $content)
    {
        //新增一条回复
        $data = array('uid' => is_login(), 'post_id' => $post_id, 'parse' => 0, 'content' => $content);
        $data = $this->create($data);
        if (!$data) return false;
        $result = $this->add($data);
        action_log('add_post_reply','GroupPostReply',$result,is_login());

        S('group_post_replylist_'.$post_id,null);
        //增加帖子的回复数
        D('GroupPost')->where(array('id' => $post_id))->setInc('reply_count');

        //更新最后回复时间
        D("GroupPost")->where(array('id' => $post_id))->setField('last_reply_time', time());
       $url= $this->sendReplyMessage(is_login(), $post_id, $content,$result);
        $this->handleAt($content,$url);

        //返回结果
        return $result;
    }


    public function handleAt($content,$url){
        D('ContentHandler')->handleAtWho($content,$url);
    }

    /**
     * @param $uid
     * @param $post_id
     * @param $content
     * @param $reply_id
     * @return string
     * @auth 陈一枭
     */
    private function sendReplyMessage($uid, $post_id, $content,$reply_id)
    {
        $limit = 10;
        $map['status']=1;
        $map['post_id']=$post_id;
        $count = D('GroupPostReply')->where($map)->count();
        $pageCount = ceil($count / $limit);
        //增加微博的评论数量
        $user = query_user(array('nickname', 'space_url'), $uid);
        $post = D('GroupPost')->find($post_id);
        $title = $user['nickname'] . '回复了您的帖子。';
        $content = '回复内容：' . mb_substr(op_t($content), 0, 20);
        $url = U('Group/Index/detail', array('id' => $post_id,'page'=>$pageCount)).'#'.$reply_id;
        $from_uid = $uid;
        D('Message')->sendMessage($post['uid'], $content, $title, $url, $from_uid, 2, null, 'reply', $post_id,$reply_id);

        return $url;
    }

    public function getReplyList($map,$order,$page,$limit){
         $replyList = S('group_post_replylist_'.$map['post_id']);
         if($replyList == null){
            $replyList = D('GroupPostReply')->where($map)->order($order)->select();
            foreach ($replyList as &$reply) {
                $reply['user'] = query_user(array('avatar128', 'nickname', 'space_url', 'icons_html','rank_link'), $reply['uid']);
                $reply['lzl_count'] = D('group_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $reply['id'])->count();
            }
            unset($reply);
            S('group_post_replylist_'.$map['post_id'],$replyList,60);
        }
        $replyList = getPage($replyList,$limit,$page);
        return $replyList;
    }

    public function delPostReply($id){
        $reply = D('GroupPostReply')->where('id='.$id)->find();
        $data['status']=0;
        CheckPermission(array($reply['uid']))  &&  $res = $this->where('id='.$id)->save($data);
        if($res){
            $lzlReply_idlist=D('GroupLzlReply')->where('is_del=0 and to_f_reply_id=' . $id)->field('id')->select();
            $info['is_del']=1;
            foreach($lzlReply_idlist as $val){
                D('GroupLzlReply')->where('id=' . $val['id'])->save($info);
                D('GroupPost')->where(array('id' => $reply['post_id']))->setDec('reply_count');
            }
        }
        D('GroupPost')->where(array('id' => $reply['post_id']))->setDec('reply_count');
        S('group_post_replylist_'.$reply['post_id'],null);
        return $res;
    }


}