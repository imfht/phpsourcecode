<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章的评论投票控制器
 */
class Commentvote extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
        $this->load->model('commentvote_model');
    }

    /**
     * 评论投票
     */
    public function vote()
    {
        $comment_id = $this->input->post('comment_id');
        $vote_type = $this->input->post('vote_type');

        $comment = $this->comment_model->get($comment_id);

        //判断是否已投票
        $vote = $this->commentvote_model->getByCommentIdAndUserId($comment_id, $this->user['id']);
        if (is_array($vote)) {
            $this->result['error_code'] = -200017;
            return;
        }

        $vote = array(
            'article_id' => $comment['article_id'],
            'comment_id' => $comment_id,
            'user_id' => $this->user['id'],
            'vote_type' => $vote_type,
        );
        //添加投票记录
        $this->commentvote_model->add($vote);
        //更新评论投票数
        $this->comment_model->vote($comment_id, $vote_type);
    }
}
