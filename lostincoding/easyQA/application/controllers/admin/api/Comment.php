<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 评论管理接口控制器
 */
class Comment extends AdminAPI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
        $this->load->model('commentvote_model');
        $this->load->model('article_model');
    }

    /**
     * 删除
     */
    public function del()
    {
        $comment_id = $this->input->post('comment_id');

        $comment = $this->comment_model->get($comment_id);

        //删除评论
        $this->comment_model->del($comment_id);
        //删除评论投票信息
        $this->commentvote_model->del_by_commentId($comment_id);
        //将文章评论数减1
        $this->article_model->reduce_comment_counts($comment['article_id']);
    }
}
