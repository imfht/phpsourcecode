<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Topic extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('topic_model');
        $this->data['active'] = 'topic';
    }

    public function index($page_index = 1, $page_size = 60)
    {
        $this->data['title'] = '话题管理';

        $this->data['topic_lists'] = $this->topic_model->gets($page_index, $page_size, 1);
        $this->data['topic_counts'] = $this->topic_model->get_counts();

        //分页
        $config['base_url'] = base_url("admin/topic/");
        $config['total_rows'] = $this->data['topic_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view('admin/topic_index', $this->data);
    }
}
