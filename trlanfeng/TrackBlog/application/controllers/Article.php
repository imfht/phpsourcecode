<?php

/**
 * 文章显示
 */
class Article extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('article_model');
        $this->load->model('category_model');
    }

    public function index($id)
    {
        $this->show($id);
    }

    public function show($id)
    {
        $data = $this->article_model->getOne($id);
        $data['datetime'] = date('Y-m-d', $data['times']);
        $data['tagarray'] = explode(',',$data['tags']);
        $filter = array();
        $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
        $data['catlist'] = $catList;
        $data['contentType'] = "article";
        $data['catname'] = $this->getCatnameById($data['cat'])['name'];
        $data['catnickname'] = $this->getCatnameById($data['cat'])['nickname'];
        $this->load->view('templates/'.TEMPLATES.'/header', $data);
        $this->load->view('templates/'.TEMPLATES.'/article', $data);
        $this->load->view('templates/'.TEMPLATES.'/footer');
    }

    public function getCatnameById($id)
    {
        return $cat = $this->category_model->getOne($id);
    }
}

?>