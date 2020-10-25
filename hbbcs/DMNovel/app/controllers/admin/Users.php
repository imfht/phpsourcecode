<?php

/**
 * Created by PhpStorm.
 * User: joe
 * Date: 17-1-13
 * Time: 上午9:46
 */
class Users extends CI_Controller {

    public $style, $user;

    function __construct() {
        parent::__construct();
        $this->load->model('story_model', 'story');
        $this->style = get_cookie('style') ? 'bootstrap/' . get_cookie('style') : 'bootstrap.min';
        $this->user  = $this->session->DMN_USER;
        if ($this->user['level'] < 7) {
            show_error('您没有权限查看此内容。');
        }
    }

    function index() {
        $data['style']=$this->style;
        $this->load->view('admin/users',$data);
    }

    function datatable() {
        $this->load->library('Datatables');
        $search = $this->input->get_post('search');

        if ($search['value']) {
            $this->datatables->like('name',$search['value']);
        }

        $this->datatables->select("*", false)
            ->from('users')
            ->add_column('DT_RowId', '$1', 'id')
            ->add_column('action', <<<ETO
            <div class="btn-group btn-group-sm">

                                <a href="#"  class="btn btn-default editUser" title="编辑用户">
                                    <i class="icon-edit"></i>
                                </a>
                                <a href="#" class="btn btn-default deleteUser" title="删除用户">
                                    <i class="icon-trash"></i>
                                </a>
                    </div>

ETO
            );

        echo $this->datatables->generate();
    }

    function delete($id) {
        if (!$id) {
            show_error('没有选择要删除的用户。');
        }

        $this->db->where('id',$id)->delete('users');
    }

}