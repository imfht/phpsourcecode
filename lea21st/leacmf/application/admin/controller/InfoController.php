<?php

namespace app\admin\controller;

use app\admin\model\Admin;
use app\common\library\Hash;
use think\Db;
use think\Validate;

class InfoController extends BaseController
{
    /**
     * 用户个人中心
     * @return mixed
     */
    public function user()
    {
        $info = app()->user;
        $this->assign('info', $info);
        return view();
    }

    /**
     * 更新昵称
     */
    public function updateNickname()
    {
        $nickname = $this->request->post('nickname', '', 'trim');
        if (!$nickname) {
            $this->error('昵称不能为空');
        }
        if (Db::name('admin')->where('id', app()->user->id)->setField('nickname', $nickname) !== false) {
            app()->rbac->refresh();
            $this->success('修改成功');
        }
        $this->success('修改失败');
    }

    //更新头像
    public function updateFace()
    {
        $face = $this->request->post('face', '', 'trim');
        if (Db::name('admin')->where('id', app()->user->id)->setField('face', $face) !== false) {
            app()->rbac->refresh();
            $this->success('修改成功');
        }

        $this->success('修改失败');
    }

    //修改密码
    public function updatePassword()
    {
        $post     = $this->request->post();
        $validate = new Validate([
            'old_password|密码'  => 'require|length:6,16',
            'password|密码'      => 'require|length:6,16',
            're_password|重复密码' => 'require|length:6,16'
        ], [
            'password.length' => '密码的长度为6到16位'
        ]);
        extract($post);
        if (!$validate->check($post)) {
            $this->error($validate->getError());
        }


        $admin = Admin::find(app()->user->id);
        if (!Hash::check($post['old_password'], $admin->password)) {
            $this->error('旧密码错误');
        }
        if ($post['password'] != $post['re_password']) {
            $this->error('两次密码输入不一致');
        }
        $password = Hash::hash($post['password']);
        if (Db::name('admin')->where('id', app()->user->id)->setField('password', $password) !== false) {
            $this->success('修改成功');
        }

        $this->success('修改失败');
    }
}