<?php
/**
 * 评论
 * User: Administrator
 * Date: 2018/8/24
 * Time: 12:34
 */
class CommentController extends Action{

    public function addComment(){
        $ser=new CommentService();
        $rs=$ser->addComment($_POST);
        $this->sendNotice($rs['message'],null,$rs['status']);
    }
}