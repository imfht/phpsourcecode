<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('codercalendar');
        $this->load->model('article_model');
        $this->load->model('comment_model');
        $this->load->model('maopao_model');
        $this->data['active'] = 'all';
    }

    public function index($type = 'latest', $page_index = 1, $page_size = 50)
    {
        if (!in_array($type, array('latest', 'fine'))) {
            redirect();
        }

        if ($type == 'latest') {
            $this->data['title'] = '最新';
            $this->data['article_lists'] = $this->article_model->gets_by_latest(0, $page_index, $page_size);
            $this->data['article_counts'] = $this->article_model->get_counts(0);
        } else if ($type == 'fine') {
            $this->data['title'] = '精华';
            $this->data['article_lists'] = $this->article_model->gets_by_fine(0, $page_index, $page_size);
            $this->data['article_counts'] = $this->article_model->get_counts_by_fine(0);
        }

        //分页
        $config['base_url'] = base_url("{$type}/");
        $config['total_rows'] = $this->data['article_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 2;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->data['q_by_view_hot_lists'] = $this->article_model->gets_by_hot(1, 0, 7, 1, 10);
        $this->data['q_by_comment_hot_lists'] = $this->article_model->gets_by_hot(1, 1, 7, 1, 10);
        $this->data['comment_top_user_lists'] = $this->comment_model->get_top_users(1, 12);

        //冒泡
        $this->data['latest_maopao_lists'] = $this->maopao_model->gets_by_latest(1, 6, $this->user['id']);
        $this->data['hot_maopao_lists'] = $this->maopao_model->gets_by_hot(1, 7, 1, 6, $this->user['id']);

        //程序员老黄历
        $this->data['codercalendar'] = $this->codercalendar->showLucky();

        $this->data['active_nav'] = $type;
        $this->load->view("{$this->theme_id}/index", $this->data);
    }
}
