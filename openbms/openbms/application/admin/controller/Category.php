<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Category extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->fetch('index', ['list' => list_to_level(model('category')->order('sort_order asc')->select())]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('category', $this->request->param()) === true) {
                insert_admin_log('添加了分类');
                $this->success('添加成功', url('admin/category/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['category' => list_to_level(model('category')->order('sort_order asc')->select())]);
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            if ($this->update('category', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了分类');
                $this->success('修改成功', url('admin/category/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', [
            'data'     => model('category')->where('id', input('id'))->find(),
            'category' => list_to_level(model('category')->order('sort_order asc')->select()),
        ]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('category', $this->request->param()) === true) {
                insert_admin_log('删除了分类');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
