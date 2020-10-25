<?php
defined('BASEPATH') or exit('No direct script access allowed');

class U extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->data['active'] = 'u';
    }

    public function index($page_index = 1, $page_size = 60)
    {
        $this->data['title'] = '提问管理';

        $this->data['u_lists'] = $this->user_model->gets_by_latest($page_index, $page_size);
        $this->data['u_counts'] = $this->user_model->get_counts();

        //分页
        $config['base_url'] = base_url("admin/u/");
        $config['total_rows'] = $this->data['u_counts'];
        $config['per_page'] = $page_size;
        $config['suffix'] = '/' . $page_size;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $this->data['page_html'] = $this->pagination->create_links();

        $this->load->view('admin/u_index', $this->data);
    }
}
