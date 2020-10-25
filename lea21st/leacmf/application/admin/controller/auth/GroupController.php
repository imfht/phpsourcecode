<?php

namespace app\admin\controller\auth;

use app\admin\controller\BaseController;
use think\Db;

/**
 * 用户组管理
 * Class GroupController
 * @package app\admin\controller\auth
 */
class GroupController extends BaseController
{

    /**
     * 主页面
     * @return mixed
     */
    public function index()
    {
        return view();
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists()
    {
        $list = Db::name('auth_group')->select();
        $this->assign('list', $list);

        return view();
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (!isset($post['title'])) {
                $this->error('名称不能为空');
            }
            if (Db::name('auth_group')->insert($post) !== false) {
                $this->success('操作成功');
            }

            $this->error('添加失败');
        } else {
            return $this->fetch('edit');
        }
    }

    /**
     * 修改页面和修改操作
     * @return mixed
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (!isset($post['name'])) {
                $this->error('名称不能为空');
            }
            if (Db::name('auth_group')->update($post) !== false) {
                $this->success('操作成功');
            }

            $this->error('更新失败');
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                exit('参数错误');
            }
            $info = Db::name('auth_group')->where('id', $id)->find();
            $this->assign('info', $info);

            return view();
        }
    }

    /**
     * 删除操作
     * @return json
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id <= 0) {
            $this->error('参数错误');
        }
        if (Db::name('auth_group')->delete($id) !== false) {
            Db::name('auth_group_access')->where('group_id', $id)->delete();
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    public function assigned()
    {
        if ($this->request->isPost()) {
            $rules   = $this->request->post('rules', '');
            $id      = $this->request->get('id', 0, 'intval');
            $operate = $this->request->post('operate', '', 'trim');
            $info    = Db::name('auth_group')->where('id', $id)->find();
            //增权限
            if ($operate == 'add') {
                $rules .= ',' . (string)$info['rules'];
                $rules = array_filter(array_unique(explode(',', $rules)));
            } else {
                $rules     = explode(',', $rules);
                $have_rule = explode(',', $info['rules']);
                foreach ($have_rule as $key => $val) {
                    if (in_array($val, $rules)) {
                        unset($have_rule[$key]);
                    }
                }
                $rules = $have_rule;
            }
            //过滤已经删除的
            $rules = Db::name('auth_rule')->where('id', 'in', $rules)->column('id');
            $rules = implode(',', $rules);
            if (Db::name('auth_group')->where(['id' => $id])->setField('rules', $rules) !== false) {
                $this->success('设置成功');
            }

            $this->error('删除失败');
        } else {
            $id   = $this->request->get('id', 0, 'intval');
            $ajax = $this->request->get('ajax', 0, 'intval');
            if (!$id || $id == 1) {
                exit('参数错误');
            }
            $info = Db::name('auth_group')->where('id', $id)->find();
            //已分配
            if (empty($info['rules'])) {
                $info['rules'] = 0;
            }
            $have_rule = Db::name('auth_rule')->where('id', 'in', $info['rules'])->order('name asc')->select();
            $no_rule   = Db::name('auth_rule')->where('id', 'not in', $info['rules'])->order('name asc')->select();
            if ($ajax) {
                return json(['info' => $info, 'no_rule' => $no_rule, 'have_rule' => $have_rule]);
            } else {
                return $this->fetch('', ['info' => $info, 'no_rule' => $no_rule, 'have_rule' => $have_rule]);
            }
        }
    }

}