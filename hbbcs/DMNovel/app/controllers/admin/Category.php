<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 16-4-6
 * Time: 下午4:46
 */
class Category extends CI_Controller {

    public $style,$user;

    function __construct() {
        parent::__construct();
        $this->load->model('category_model', 'category');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user = $this->session->DMN_USER;
        if ($this->user['level'] <7 ) {
            show_error('您没有权限查看此内容。');
        }
    }

    function index() {
        $data['categorys'] = $this->category->get();
        $data['style']     = $this->style;
        $this->load->view('admin/category', $data);
    }

    function add() {
        $id    = $this->input->post('id');
        $title = $this->input->post('title');

        if (!$title) {
            show_error('分类标题不能为空，请返回重新填写！！！');
        }

        $category_data = array(
            'id'    => $id,
            'title' => $title
        );
        $this->category->add($category_data);
        redirect('admin/category');
    }

    function delete($id = null) {
        if (!$id) show_error('没有选择要删除的分类。');

        $this->db->delete('category', array('id' => $id));
    }

}