<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Article extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model');
        $this->data['active'] = 'article';
    }

    public function index($page_index = 1, $page_size = 60)
    {
        $this->data['title'] = '文章管理';

        $this->data['article_lists'] = $this->article_model->gets_by_latest(0, $page_index, $page_size);
        $this->data['article_counts'] = $this->article_model->get_counts(0);

        //分页
        $config['base_url'] = base_url("admin/article/");
        $config['total_rows'] = $this->data['article_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view('admin/article_index', $this->data);
    }
}
