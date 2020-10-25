<?php
/**
 * Created by PhpStorm.
 * User: Y.c
 * Date: 2017/6/20
 * Time: 14:53
 */

namespace app\admin\controller;

use app\common\model\User;
use think\Db;

class UserController extends BaseController
{

    public function index()
    {
        return view();
    }

    public function lists()
    {
        $status     = $this->request->post('status', -1, 'intval');
        $keyword    = $this->request->post('keyword', '', 'trim');
        $range_time = $this->request->post('range_time', '', 'trim');   //开始注册时间

        $userModel = Db::name('user')->order('id desc');
        if ($keyword) {
            if (is_phone($keyword)) {
                $userModel->where('mobile', $keyword);
            } else {
                $userModel->where('nickname', 'like', '%' . $keyword . '%');
            }
        }
        //时间
        if ($range_time) {
            $range_time = range_time($range_time);
            $userModel->where('register_time', 'between', [$range_time[0], $range_time[1]]);
        }
        if ($status > -1) {
            $userModel->where('status', $status);
        }
        $users = $userModel->order('id desc')->paginate(2);
        $list  = $users->getCollection()->toArray();
        $page  = $users->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return view();
    }

    /**
     * 快速禁用
     * @return json
     */
    public function setStatus()
    {
        $id     = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && Db::name('user')->where('id', $id)->setField('status', $status) > 0) {
            $this->success('设置成功');
        }

        $this->error('更新失败');
    }


    public function edit()
    {
        if ($this->request->isPost()) {
            $post = $this->request->only(['id', 'nickname', 'face']);
            $id   = $post['id'];
            unset($post['id']);
            if (Db::name('user')->where('id', $id)->update($post) > 0) {
                $this->success('修改成功');
            }
            $this->error('修改失败');
        } else {
            $id   = $this->request->get('id', 0, 'intval');
            $user = User::get($id);
            $this->assign('user', $user);
            return view();
        }
    }

}