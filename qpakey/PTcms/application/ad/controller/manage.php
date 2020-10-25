<?php

class ManageController extends AdminController {

    /**
     * @var admodel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model = $this->model('ad');
    }


    public function indexAction() {
        $this->view->list = $this->model->field('id,name,intro,width,height,create_user_id,create_time,update_user_id,update_time,status,key')->getlist();
    }

    public function addAction() {
        if ($this->request->ispost()) {
            $param['name'] = $this->input->post('name', 'str', '');
            if (!$param['name']) {
                $this->error('请输入链接名称');
            }
            $param['key'] = $this->input->post('key', 'en', '');
            if (!$param['key']) {
                $this->error('请输入链接地址');
            }
            $param['width']          = $this->input->post('width', 'int', 0);
            $param['height']         = $this->input->post('height', 'int', 0);
            $param['code']           = $this->input->post('code', 'str', '');
            $param['intro']          = $this->input->post('intro', 'str', '');
            $param['status']         = $this->input->post('status', 'int', 1);
            $param['type']           = $this->input->post('type', 'int', 1);
            $param['create_user_id'] = $_SESSION['admin']['userid'];
            $param['create_time']    = NOW_TIME;
            if ($this->model->add($param)) {
                $this->success('添加成功', U('index'));
            } else {
                $this->error('添加失败');
            }
        }
    }

    public function editAction() {
        $id   = $this->input->request('id', 'int', 0);
        $info = $this->model->where(array('id' => $id))->find();
        if ($this->request->ispost()) {
            $param['name'] = $this->input->post('name', 'str', '');
            if (!$param['name']) {
                $this->error('请输入链接名称');
            }
            $param['width']          = $this->input->post('width', 'int', 0);
            $param['height']         = $this->input->post('height', 'int', 0);
            $param['code']           = $this->input->post('code', 'str', '');
            $param['intro']          = $this->input->post('intro', 'str', '');
            $param['status']         = $this->input->post('status', 'int', 1);
            $param['type']           = $this->input->post('type', 'int', 1);
            $param['update_user_id'] = $_SESSION['admin']['userid'];
            $param['update_time']    = NOW_TIME;
            $param['id']             = $id;
            if ($this->model->edit($param)) {
                $this->success('修改成功', U('index'));
            } else {
                $this->error('修改失败');
            }
        }
        $this->view->info = $info;
    }

    public function showAction() {
        $id               = $this->input->request('id', 'int', 0);
        $this->view->info = $this->model->where(array('id' => $id))->find();
    }
}