<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-20
 * Time: 下午3:44
 */
class Search extends CI_Controller {

    public $style;

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->load->model('chapter_model', 'chapter');
        $this->load->model('Category_model', 'category');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
    }

    public function index($page = 0) {

        $search = $this->input->get('search');
        if (!$search) show_error('请输入搜索内容');
        $where = "`author` like '%{$search}%' or `title` like '%{$search}%'";

        $this->load->library('pagination');

        $per_page=20;

        $data['title']      = '搜索：' . $search;
        $data['categories'] = $this->category->get();
        $data['stories']    = $this->story->get(null, $per_page, $page, $where);
        $data['search']     = $search;
        $data['pages']      = $this->pagination->create_links(); //创建分页
        $data['per_page']   = 20;
        $data['all'] =$this->story->all($where);

        $data['style'] = $this->style;
        $data['user']  = $this->session->DMN_USER;

        $this->load->view('header', $data);
        $this->load->view('category');
        $this->load->view('footer');
    }

}