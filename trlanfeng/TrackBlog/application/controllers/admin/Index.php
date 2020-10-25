<?php

/**
 * 文章管理
 */
class Index extends TB_Admin
{

    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('article_model');
        $this->load->model('category_model');
    }

    public function index()
    {
        $this->load->view('admin/header');
        $this->load->view('admin/index');
        $this->load->view('admin/footer');
    }

    public function getCatnameById($id)
    {
        $cat = $this->category_model->getOne($id);
        return $cat['name'];
    }


}

?>