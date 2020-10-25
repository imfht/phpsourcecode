<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/25
 * Time: 13:55
 */
class CommentadminController extends  AdminController{


    public function getAllComment(){

        $ser=new CommentService();
        $rs=$ser->getAllComment();
        $this->view()->assign('rs',$rs);
        $this->view()->display("file:comment/comment.html");
    }

    public function removeCommentById(){
        $ser=new CommentService();
        $rs=$ser->removeCommentById($_POST['id']);
        $this->sendNotice("删除成功",null,true);

    }

}