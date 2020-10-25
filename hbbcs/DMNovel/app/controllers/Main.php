<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public $style;

    function __construct() {
        parent::__construct();
        $this->load->model('setting_model', 'setting');
        $this->title = $this->setting->get('title');
        $this->style = get_cookie('style')?'bootstrap/'.get_cookie('style'):'bootstrap.min';
        $this->load->model('users_model', 'users');
    }

    public function index() {
        $this->load->model('Category_model', 'category');
        $this->load->model('story_model', 'story');

        $data['title']      = $this->title;
        $data['update']     = $this->story->get(null, 5, 0, ['approve' => 1],'', 'last_update', 'DESC');
        $data['categories'] = $this->category->get();

        $data['average'] = $this->story->get(null, 5, 0, ['approve' => 1],'id,title,average', 'average', 'DESC');
        $data['mark'] = $this->story->get(null, 5, 0, ['approve' => 1],'id,title,mark', 'mark', 'DESC');

        //获取每个分类的最新更新5条记录
        foreach ($data['categories'] as $category) {
            $category_update[] = array(
                'category' => $category,
                'stories'  => $this->story->get(null, 5, 0, ['category' => $category['id'],'approve' => 1],'id,title,last_update', 'last_update', 'DESC')
            );
        }

        $data['user'] = $this->session->DMN_USER;
        $data['style']=$this->style;
        $data['category_update'] = $category_update;

        $this->load->view('main', $data);
    }


}
