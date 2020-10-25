<?php

class UserController extends AdminController {

    /**
     * @var adminUserModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model = $this->model('adminuser');
    }

    public function indexAction() {
        $this->view->list = $this->model->getlist();
    }

    public function addAction() {
        if ($this->request->ispost()) {
            $param['user_id']    = $this->db('user')->where(array('name' => $this->input->post('name', 'username', '')))->getfield('id');
            $param['intro']          = $this->input->post('intro', 'str');
            $param['group_id']       = $this->input->post('groupid', 'int', 0);
            $param['status']         = $this->input->post('status', 'int', 0);
            $param['login_num']      = 0;
            if ($this->model->add($param)) {
                $this->success('添加成功', U('index'));
            } else {
                $this->error('添加失败');
            }
        }
        $this->view->grouplist = $this->db('admin_group')->field('id,name')->select();
    }

    public function editAction() {
        $id   = $this->input->request('id', 'int', 0);
        $info = $this->db('admin_user')->field('id,user_id,group_id,intro,status')->where(array('id' => $id))->find();
        if ($this->request->ispost()) {
            $param['intro']          = $this->input->post('intro', 'str');
            $param['group_id']       = $this->input->post('groupid', 'int', 0);
            $param['status']         = $this->input->post('status', 'int', 0);
            $param['id']             = $id;
            if ($this->model->edit($param)) {
                $this->success('修改成功', U('index'));
            } else {
                $this->error('修改失败');
            }
        }
        $this->view->grouplist = $this->db('admin_group')->field('id,name')->select();
        $info['name']          = $this->model->get('user', $info['user_id'], 'name');
        $this->view->info      = $info;
    }

    public function ajaxAction() {
        $id    = $this->input->request('id', 'int', 0);
        $value = $this->input->post('param', 'username', '');
        if ($value) {
            if ($passport_id = $this->db('user')->where(array('name' => $value))->getfield('id')) {
                $oid = $this->db('admin_user')->where(array('user_id' => $passport_id))->getfield('id');
                if ($oid && $oid != $id) {
                    $data = array('status' => 'n', 'info' => '您输入的用户名已经使用了');
                } else {
                    $data = array('status' => 'y', 'info' => '帐号可以使用');
                }
            } else {
                $data = array('status' => 'n', 'info' => '您输入的用户名不存在');
            }
        } else {
            $data = array('status' => 'n', 'info' => '输入的用户名有误');
        }
        $this->ajax($data);
    }
}