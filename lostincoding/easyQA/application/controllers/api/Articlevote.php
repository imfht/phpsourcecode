<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 文章投票控制器
 */
class Articlevote extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->load->model('articlevote_model');
    }

    /**
     * 文章投票
     */
    public function vote()
    {
        $article_id = $this->input->post('article_id');
        $vote_type = $this->input->post('vote_type');

        //判断是否已投票
        $vote = $this->articlevote_model->get_by_id_and_userId($article_id, $this->user['id']);
        if (is_array($vote)) {
            $this->result['error_code'] = -200016;
            return;
        }

        $vote = array(
            'article_id' => $article_id,
            'user_id' => $this->user['id'],
            'vote_type' => $vote_type,
        );
        //添加投票记录
        $this->articlevote_model->add($vote);
        //更新文章投票数
        $this->article_model->vote($article_id, $vote_type);
    }
}
