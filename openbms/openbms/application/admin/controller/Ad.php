<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Ad extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $category = [
                'index' => '首页轮播',
                'other' => '其它图片',
            ];
            $this->assign('category', $category);
        }
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['name'])) {
            $where['name'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['category'])) {
            $where['category'] = $param['category'];
        }
        $list = model('ad')->order('id desc')->where($where)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('ad', $this->request->param()) === true) {
                insert_admin_log('添加了广告图');
                $this->success('添加成功', url('admin/ad/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            if ($this->update('ad', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了广告图');
                $this->success('修改成功', url('admin/ad/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('ad')->where('id', input('id'))->find()]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('ad', $this->request->param()) === true) {
                insert_admin_log('删除了广告图');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
