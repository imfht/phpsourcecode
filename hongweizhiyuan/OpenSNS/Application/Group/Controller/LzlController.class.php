<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:30
 */

namespace Group\Controller;

use Think\Controller;

define('TOP_ALL', 1);
define('TOP_FORUM', 2);

class LzlController extends GroupController
{


    public function  lzllist($to_f_reply_id, $page = 1,$p=1)
    {
        $to_f_reply_id=intval($to_f_reply_id);
        $page=intval($page);
        $p=intval($p);
        $limit = 5;
        $list = D('GroupLzlReply')->getLZLReplyList($to_f_reply_id,'create_time asc',$page,$limit);

        $post = D('GroupPost')->find($list[0]['post_id']);
        $this->assign('post', $post);
        $totalCount = D('group_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $to_f_reply_id)->count();
        $data['to_f_reply_id'] = $to_f_reply_id;
        $pageCount = ceil($totalCount / $limit);
        $html = getPageHtml('changePage', $pageCount, $data, $page);
        $this->assign('lzlList', $list);
        $this->assign('html', $html);
        $this->assign('p', $p);
        $this->assign('nowPage', $page);
        $this->assign('totalCount', $totalCount);
        $this->assign('limit', $limit);
        $this->assign('count', count($list));
        $this->assign('to_f_reply_id', $to_f_reply_id);
        $this->display();
    }


    public function doSendLZLReply($post_id, $to_f_reply_id, $to_reply_id, $to_uid, $content,$p=1)
    {

        $post_id=intval($post_id);
        $to_f_reply_id=intval($to_f_reply_id);
        $to_reply_id=intval($to_reply_id);
        $to_uid=intval($to_uid);
        $content=op_t($content);
        $p=intval($p);

        if (get_user_action('Group', 'reply', 'ban')) {
            $this->error('您已被禁言，联系管理');
        }
        //确认用户已经登录
        $this->requireLogin();

        $group_id = $this-> getGroupIdByPost($post_id);
        if(!$this->isGroupAllowPublish($group_id)){
            $this->error('只允许群组成员回复');
        }


        //写入数据库
        $model = D('GroupLzlReply');
        $before=getMyScore();
        $tox_money_before=getMyToxMoney();
       
        $result = $model->addLZLReply($post_id, $to_f_reply_id, $to_reply_id, $to_uid, $content,$p);
        $after=getMyScore();
        $tox_money_after=getMyToxMoney();


        //增加活跃度
        D('Group')->where(array('id'=>$group_id))->setInc('activity');
        D('GroupMember')->where(array('group_id'=>$group_id,'uid'=>is_login()))->setInc('activity');


        if (!$result) {
            $this->error('发布失败：' . $model->getError());
        }
        //显示成功页面
        $totalCount = D('group_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $to_f_reply_id)->count();
        $limit = 5;
        $pageCount = ceil($totalCount / $limit);
        exit(json_encode(array('status'=>1,'info'=>'回复成功。'.getScoreTip($before,$after).getToxMoneyTip($tox_money_before,$tox_money_after),'url'=>$pageCount)));
    }


public function delLZLReply($id){
    $id=intval($id);

    $this->requireLogin();
    $data['post_reply_id']=D('GroupLzlReply')->where('id='.$id)->getfield('to_f_reply_id');
    $res= D('GroupLzlReply')->delLZLReply($id);
    $data['lzl_reply_count']=D('GroupLzlReply')->where('is_del=0 and to_f_reply_id='.$data['post_reply_id'])->count();
    $res &&   $this->success($res,'',$data);
    !$res &&   $this->error('');
}

}