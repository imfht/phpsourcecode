<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-8
 * Time: PM4:30
 */

namespace Forum\Controller;

use Think\Controller;

define('TOP_ALL', 1);
define('TOP_FORUM', 2);

class LzlController extends Controller
{


    public function  lzllist($to_f_reply_id, $page = 1,$p=1)
    {
        $limit = 5;
        $list = D('ForumLzlReply')->getLZLReplyList($to_f_reply_id,'ctime asc',$page,$limit);
        $totalCount = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $to_f_reply_id)->count();
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

        //确认用户已经登录
        $this->requireLogin();
        $this->checkAuth('Forum/Lzl/doSendLZLReply',get_expect_ids(0,0,$post_id,0),L('_INFO_AUTHORITY_REPLY_NONE_').L('_EXCLAMATION_'));
        $this->checkActionLimit('forum_lzl_reply','Forum',null,get_uid());
        //写入数据库
        $model = D('ForumLzlReply');
        $before=getMyScore();
        $result = $model->addLZLReply($post_id, $to_f_reply_id, $to_reply_id, $to_uid, op_t($content),$p);
        $after=getMyScore();
        if (!$result) {
            $this->error(L('_ERROR_PUBLISH_').L('_COLON_') . $model->getError());
        }
        action_log('forum_lzl_reply','Forum',$result,get_uid());
        //显示成功页面
        $totalCount = D('forum_lzl_reply')->where('is_del=0 and to_f_reply_id=' . $to_f_reply_id)->count();
        $limit = 5;
        $pageCount = ceil($totalCount / $limit);
        exit(json_encode(array('status'=>1,'info'=>L('_SUCCESS_REPLY_').L('_PERIOD_').getScoreTip($before,$after),'url'=>$pageCount)));
    }

    private function requireLogin()
    {
        if (!is_login()) {
            $this->error(L('_ERROR_LOGIN_'));
        }
    }

    public function delLZLReply($id){
        $this->requireLogin();
        $this->checkAuth('Forum/Lzl/delLZLReply',get_expect_ids($id),L('_INFO_AUTHORITY_REPLY_DELETE_').L('_EXCLAMATION_'));
        $this->checkActionLimit('forum_lzl_del_reply','Forum',null,get_uid());
        $Lzlreply=D('ForumLzlReply')->where('id='.$id)->find();
        $data['post_reply_id']=$Lzlreply['to_f_reply_id'];
        $res= D('ForumLzlReply')->delLZLReply($id);
        $data['lzl_reply_count']=D('ForumLzlReply')->where('is_del=0 and to_f_reply_id='.$data['post_reply_id'])->count();
        action_log('forum_lzl_del_reply','Forum',$id,get_uid());
        $res &&   $this->success($res,'',$data);
        !$res &&   $this->error('');
    }
}