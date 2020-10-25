<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 冒泡投票控制器
 */
class Maopaovote extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopao_model');
        $this->load->model('maopaovote_model');
    }

    /**
     * 冒泡投票
     */
    public function vote()
    {
        $maopao_id = $this->input->post('maopao_id');
        $vote_type = $this->input->post('vote_type');

        //判断是否已投票
        $vote = $this->maopaovote_model->get_by_id_and_userId($maopao_id, $this->user['id']);
        if (is_array($vote)) {
            $this->result['error_code'] = -200310;
            return;
        }

        $vote = array(
            'maopao_id' => $maopao_id,
            'user_id' => $this->user['id'],
            'vote_type' => $vote_type,
        );
        //添加投票记录
        $this->maopaovote_model->add($vote);
        //更新冒泡投票数
        $this->maopao_model->vote($maopao_id, $vote_type);
    }
}
