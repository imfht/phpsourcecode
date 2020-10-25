<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 冒泡评论投票控制器
 */
class Maopaocommentvote extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopaocomment_model');
        $this->load->model('maopaocommentvote_model');
    }

    /**
     * 冒泡评论投票
     */
    public function vote()
    {
        $comment_id = $this->input->post('comment_id');
        $vote_type = $this->input->post('vote_type');

        $comment = $this->maopaocomment_model->get($comment_id);

        //判断是否已投票
        $vote = $this->maopaocommentvote_model->getByCommentIdAndUserId($comment_id, $this->user['id']);
        if (is_array($vote)) {
            $this->result['error_code'] = -200311;
            return;
        }

        $vote = array(
            'maopao_id' => $comment['maopao_id'],
            'comment_id' => $comment_id,
            'user_id' => $this->user['id'],
            'vote_type' => $vote_type,
        );
        //添加冒泡投票记录
        $this->maopaocommentvote_model->add($vote);
        //更新冒泡评论投票数
        $this->maopaocomment_model->vote($comment_id, $vote_type);
    }
}
