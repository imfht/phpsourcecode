<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Article extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->request->isGet()) {
            $this->assign('category', list_to_level(model('category')->order('sort_order asc')->select()));
        }
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['cid'])) {
            $where['cid'] = $param['cid'];
        }
        if (isset($param['is_top'])) {
            $where['is_top'] = $param['is_top'];
        }
        if (isset($param['is_hot'])) {
            $where['is_hot'] = $param['is_hot'];
        }
        if (isset($param['status'])) {
            $where['status'] = $param['status'];
        }
        $list = model('article')->with('category')->order('id desc')->where($where)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('article', $this->request->param()) === true) {
                insert_admin_log('添加了文章');
                $this->success('添加成功', url('admin/article/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if (is_array($param['id'])) {
                $data = [];
                foreach ($param['id'] as $v) {
                    $data[] = ['id' => $v, $param['name'] => $param['value']];
                }
                $result = $this->saveAll('article', $data, input('_verify', true));
            } else {
                $result = $this->update('article', $param, input('_verify', true));
            }
            if ($result === true) {
                insert_admin_log('修改了文章');
                $this->success('修改成功', url('admin/article/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('article')->get(input('id'))]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('article', $this->request->param()) === true) {
                insert_admin_log('删除了文章');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
