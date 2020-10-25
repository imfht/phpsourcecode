<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class User extends AdminBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->fetch('index', ['list' => model('user')->order('id desc')->paginate(config('page_number'))]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            empty($param['password']) && $this->error('密码不能为空');
            if ($this->insert('user', $param) === true) {
                insert_admin_log('添加了用户');
                $this->success('添加成功', url('admin/user/index'));
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
            if (empty($param['password'])) {
                unset($param['password']);
            }
            if ($this->update('user', $param, input('_verify', true)) === true) {
                insert_admin_log('修改了用户');
                $this->success('修改成功', url('admin/user/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('user')->where('id', input('id'))->find()]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('user', $this->request->param()) === true) {
                insert_admin_log('删除了用户');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function export()
    {
        $data = collection(model('user')->field('id,username,mobile')->order('id desc')->select())->toArray();
        array_unshift($data, ['ID', '用户名', '手机号']);
        insert_admin_log('导出了用户');
        export_excel($data, date('YmdHis'));
    }

    public function log()
    {
        return $this->fetch('log', ['list' => model('userLog')->order('create_time desc')->paginate(config('page_number'))]);
    }

    public function truncate()
    {
        if ($this->request->isPost()) {
            db()->query('TRUNCATE ' . config('database.prefix') . 'user_log');
            $this->success('操作成功');
        }
    }
}
