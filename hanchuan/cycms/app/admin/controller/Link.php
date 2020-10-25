<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;

class Link extends Common
{
    public function index($act=null)
    {
        if ($act=='del') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $ids = input('post.');

            if (!empty($ids)) {
                $r = Db::name('link')->delete($ids['ids']);
                if ($r) {
                    addlog('删除友情链接，ID：'.implode(',', $ids['ids']), $this->user['username']);
                    return $this->success('恭喜，友情链接删除成功！', url('admin/link/index'));
                }
            }

            return $this->error('请选择需要删除的选项！');
        }

        $list = Db::name('link')->order('o asc')->paginate(25);
        View::assign('list', $list);
        return View::fetch();
    }

    public function add()
    {
        return View::fetch('form');
    }

    public function edit($id)
    {
        $id = intval($id);
        $link = Db::name('link')->where(['id'=>$id])->find();
        if (!$link) {
            return $this->error('参数错误，请重试！');
        }
        View::assign('link', $link);
        return View::fetch('form');
    }

    public function save()
    {
        if (!Request::isPost()) {
            return $this->error('参数错误，请重试！');
        }

        $data = input('post.');
        if ($data['title']=='') {
            return $this->error('标题不能为空！');
        }

        $data['id'] = intval($data['id']);
        $data['o'] = intval($data['o']);
        if ($data['id']) {
            $r = Db::name('link')->where(['id'=>$data['id']])->update($data);
            addlog('编辑友情链接，ID：'.$data['id'], $this->user['username']);
        } else {
            unset($data['id']);
            $r = Db::name('link')->insert($data);
            addlog('新增友情链接，标题：'.$data['title'], $this->user['username']);
        }
        if ($r) {
            return $this -> success('恭喜，操作成功！', url('admin/link/index'));
        } else {
            return $this->error('系统错误，请重试！');
        }
    }
}
