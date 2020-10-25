<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Config extends AdminBase
{
    protected $noAuth = ['sendEmail'];

    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $param = $this->request->param();
        $where = [];
        if (isset($param['title'])) {
            $where['title'] = ['like', "%" . $param['title'] . "%"];
        }
        if (isset($param['group'])) {
            $where['group'] = $param['group'];
        }
        $list = model('config')->order('sort_order asc,id asc')->where($where)
            ->paginate(config('page_number'), false, ['query' => $param]);
        return $this->fetch('index', ['list' => $list]);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('config', $this->request->param()) === true) {
                insert_admin_log('添加了基本配置');
                $this->success('添加成功', url('admin/config/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            if ($this->update('config', $this->request->param(), input('_verify', true)) === true) {
                insert_admin_log('修改了基本配置');
                $this->success('修改成功', url('admin/config/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('config')->where('id', input('id'))->find()]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('config', $this->request->param()) === true) {
                insert_admin_log('删除了基本配置');
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function setting()
    {
        if ($this->request->isPost()) {
            $data = [];
            foreach ($this->request->param() as $k => $v) {
                $data[] = ['id' => hashids_decode($k), 'value' => $v];
            }
            if ($this->saveAll('config', $data, false) === true) {
                clear_cache();
                insert_admin_log('更新基本设置');
                $this->success('保存成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $list = [];
        foreach (config('configGroup') as $k => $v) {
            $list[$k]['name']   = $v;
            $list[$k]['config'] = model('config')->where([
                'group'  => $k,
                'status' => 1,
            ])->order('sort_order asc')->select();
        }
        return $this->fetch('setting', ['list' => $list]);
    }

    public function system()
    {
        if ($this->request->isPost()) {
            $data = [];
            foreach ($this->request->param() as $k => $v) {
                $data[] = ['name' => $k, 'value' => $v];
            }
            if ($this->saveAll('system', $data) === true) {
                clear_cache();
                insert_admin_log('更新系统设置');
                $this->success('保存成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data = [];
        foreach (model('system')->select() as $v) {
            $data[$v['name']] = $v['value'];
        }
        return $this->fetch('system', ['data' => $data]);
    }

    public function upload()
    {
        if ($this->request->isPost()) {
            model('system')->save(['value' => serialize($this->request->param())], ['name' => 'upload_image']);
            clear_cache();
            insert_admin_log('修改了上传设置');
            $this->success('保存成功');
        }
        $data = model('system')->where('name', 'upload_image')->find();
        return $this->fetch('upload', ['data' => unserialize($data['value'])]);
    }

    public function email()
    {
        if ($this->request->isPost()) {
            model('system')->save(['value' => serialize($this->request->param())], ['name' => 'email_server']);
            clear_cache();
            insert_admin_log('修改了邮件设置');
            $this->success('保存成功');
        }
        $data = model('system')->where('name', 'email_server')->find();
        return $this->fetch('email', ['data' => unserialize($data['value'])]);
    }

    // 测试发送
    public function sendEmail()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            empty($param['email']) && $this->error('测试邮箱不能为空');
            !check_email($param['email']) && $this->error('测试邮箱格式错误');
            if (send_email($param['email'], '发送成功', '这是一封测试的邮件！', $param)) {
                $this->success('发送成功');
            } else {
                $this->error('发送失败');
            }
        }
    }
}
