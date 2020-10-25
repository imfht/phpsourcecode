<?php

namespace app\admin\controller\auth;

use app\admin\controller\BaseController;
use app\admin\model\Admin;
use app\common\library\Hash;
use think\Db;

/**
 * 菜单规则管理
 * Class RuleController
 * @package app\admin\controller\auth
 */
class AdminController extends BaseController
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
        $list       = Db::name('admin')->select();
        $auth_group = Db::name('auth_group')->column('name', 'id');
        foreach ($list as $key => $val) {
            $role = Db::name('auth_group_access')->where('uid', $val['id'])->column('group_id');
            if ($role) {
                foreach ($role as &$v) {
                    $v = isset($auth_group[$v]) ? $auth_group[$v] : '';
                }
            }
            $list[$key]['role'] = implode(',', $role);
        }
        return view('', [
            'list' => $list,
        ]);
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $post = $this->request->post();
            if (!$post['password']) {
                $post['password'] = $post['username'];
            }

            $validate = new \app\admin\validate\Admin();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            $post['password'] = Hash::hash($post['password']);

            $admin = new Admin();
            if ($admin->allowField(true)->save($post) !== false) {
                if (!empty($post['group'])) {
                    Db::name('auth_group_access')->insert(['uid' => $admin->id, 'group_id' => $post['group']]);
                }
                $this->success('添加成功');
            }
            $this->error('添加失败');
        } else {
            //获取用户组
            $group = Db::name('auth_group')->field('id,name,status')->where('id', 'neq', '1')->select();
            $this->assign('group', $group);
            return view('edit');
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
            if ($post['id'] == 1) {
                $this->error('超级管理员无法修改，请在个人信息页面修改');
            }
            if ($post['password']) {
                $post['password'] = Hash::hash($post['password']);
            } else {
                unset($post['password']);
            }

            $validate = new \app\admin\validate\Admin();
            if (!$validate->check($post)) {
                $this->error($validate->getError());
            }

            if ((new Admin())->allowField(true)->isUpdate(true)->save($post) !== false) {
                if (!empty($post['group'])) {
                    Db::name('auth_group_access')->where(['uid' => $post['id']])->delete();
                    Db::name('auth_group_access')->insert(['uid' => $post['id'], 'group_id' => $post['group']]);
                }
                $this->success('修改成功');
            }
            $this->error('修改失败');
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('参数错误');
            }
            $info = Admin::get($id);
            //获取该用户的用户组
            $access_group = Db::name('auth_group_access')->where(['uid' => $id])->column('group_id');
            $group        = Db::name('auth_group')->field('id,name,status')->where('id', 'neq', '1')->select();
            $this->assign('group', $group);
            $this->assign('info', $info);
            $this->assign('access_group', $access_group);
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
        if (Admin::destroy($id) !== false) {
            Db::name('auth_group_access')->where('uid', $id)->delete();
            $this->success('删除成功');
        }

        $this->error('删除失败');
    }

}
