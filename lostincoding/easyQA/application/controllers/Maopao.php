<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maopao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('maopao_model');
        $this->load->model('maopaocomment_model');
        $this->data['active'] = 'maopao';
    }

    public function index($type = 'latest', $page_index = 1, $page_size = 50)
    {
        //冒泡
        $this->data['latest_maopao_lists'] = $this->maopao_model->gets_by_latest($page_index, $page_size, $this->user['id']);
        $this->data['hot_maopao_lists'] = $this->maopao_model->gets_by_hot(1, 7, $page_index, $page_size, $this->user['id']);

        $this->data['active_nav'] = $type;
        $this->data['title'] = '冒泡';
        $this->load->view("{$this->theme_id}/maopao/index", $this->data);
    }

    public function detail($id, $page_index = 1, $page_size = 50)
    {
        $maopao = $this->maopao_model->get($id, true, $this->user['id']);
        $user_id = isset($this->user['id']) ? $this->user['id'] : null;
        $this->data['comment_lists'] = $this->maopaocomment_model->gets_by_maopaoId($id, $page_index, $page_size, $user_id);
        $this->data['comment_counts'] = $this->maopaocomment_model->get_counts_by_maopaoId($id);

        $this->data['hot_maopao_lists'] = $this->maopao_model->gets_by_hot(1, 7, $page_index, $page_size, $this->user['id']);

        $this->data['active_nav'] = 'latest';
        $this->data['title'] = "#{$id} 冒泡";
        $this->data['maopao'] = $maopao;
        $this->load->view("{$this->theme_id}/maopao/detail", $this->data);
    }

    public function my($page_index = 1, $page_size = 50)
    {
        $this->is_signin();

        //我的冒泡
        $this->data['maopao_lists'] = $this->maopao_model->gets_by_userId($this->user['id'], $page_index, $page_size);
        $this->data['maopao_counts'] = $this->maopao_model->get_counts_by_userId($this->user['id']);

        $this->data['hot_maopao_lists'] = $this->maopao_model->gets_by_hot(1, 7, $page_index, $page_size, $this->user['id']);

        $this->data['active_nav'] = 'my_maopao';
        $this->data['title'] = '我的冒泡';
        $this->load->view("{$this->theme_id}/maopao/my", $this->data);
    }
}
