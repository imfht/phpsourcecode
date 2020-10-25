<?php

class ManageController extends AdminController {

    /**
     * @var FriendlinkModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model = $this->model('friendlink');
    }

    public function indexAction() {
        $this->view->list = $this->model->order('ordernum asc,id asc')->getlist();
    }

    public function addAction() {
        if ($this->request->ispost()) {
            $param['name'] = $this->input->post('name', 'str', '');
            if (!$param['name']) {
                $this->error('请输入链接名称');
            }
            $param['url'] = $this->input->post('url', 'url', '');
            if (!$param['url']) {
                $this->error('请输入链接地址');
            }
            $param['logo']           = $this->input->post('logo', 'str', '');
            $param['description']    = $this->input->post('description', 'str', '');
            $param['color']          = $this->input->post('color', 'str', '');
            $param['ordernum']       = $this->input->post('ordernum', 'int', 50);
            $param['status']         = $this->input->post('status', 'int', 1);
            $param['isbold']         = $this->input->post('isbold', 'int', 0);
            if ($this->model->add($param)) {
                $this->success('添加成功', U('index'));
            } else {
                $this->error('添加失败');
            }
        }
    }

    public function editAction() {
        $id   = $this->input->request('id', 'int', 0);
        $info = $this->model->find($id);
        if ($this->request->ispost()) {
            $param['name'] = $this->input->post('name', 'str', '');
            if (!$param['name']) {
                $this->error('请输入链接名称');
            }
            $param['url'] = $this->input->post('url', 'url', '');
            if (!$param['url']) {
                $this->error('请输入链接地址');
            }
            $param['logo']           = $this->input->post('logo', 'str', '');
            $param['description']    = $this->input->post('description', 'str', '');
            $param['color']          = $this->input->post('color', 'str', '');
            $param['ordernum']       = $this->input->post('ordernum', 'int', 50);
            $param['status']         = $this->input->post('status', 'int', 50);
            $param['isbold']         = $this->input->post('isbold', 'int', 50);
            $param['id']             = $id;
            if ($this->model->edit($param)) {
                $this->success('修改成功', U('index'));
            } else {
                $this->error('修改失败');
            }
        }
        $this->view->info = $info;
    }
}