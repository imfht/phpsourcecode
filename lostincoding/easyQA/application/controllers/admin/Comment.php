<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Comment extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
        $this->data['active'] = 'comment';
    }

    public function index($page_index = 1, $page_size = 60)
    {
        $this->data['title'] = '评论管理';

        $this->data['comment_lists'] = $this->comment_model->gets_by_latest($page_index, $page_size);
        $this->data['comment_counts'] = $this->comment_model->get_counts();

        //分页
        $config['base_url'] = base_url("admin/comment/");
        $config['total_rows'] = $this->data['comment_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view('admin/comment_index', $this->data);
    }
}
