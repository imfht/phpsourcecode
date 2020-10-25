<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;

class Banner extends Common
{
    public function index($act=null)
    {
        if ($act=='del') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $ids = input('post.');

            if (!empty($ids)) {
                $r = Db::name('banner')->delete($ids['ids']);
                if ($r) {
                    addlog('删除横幅，ID：'.implode(',', $ids['ids']), $this->user['username']);
                    return $this->success('恭喜，横幅删除成功！', url('admin/banner/index'));
                }
            }
            return $this->error('请选择需要删除的选项！');
        }

        $list = Db::name('banner')->order('o asc')->paginate(25);
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
        $banner = Db::name('banner')->where(['id'=>$id])->find();
        if (!$banner) {
            return $this->error('参数错误，请重试！');
        }

        View::assign('banner', $banner);
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
        if (isset($data['status'])) {
            $data['status'] = intval($data['status']);
        } else {
            $data['status'] = 0;
        }

        if ($data['id']) {
            $r = Db::name('banner')->where(['id'=>$data['id']])->update($data);
            addlog('编辑横幅，ID：'.$data['id'], $this->user['username']);
        } else {
            unset($data['id']);
            $r = Db::name('banner')->insert($data);
            addlog('新增横幅，标题：'.$data['title'], $this->user['username']);
        }
        if ($r) {
            return $this -> success('恭喜，操作成功！', url('admin/banner/index'));
        } else {
            return $this->error('系统错误，请重试！');
        }
    }
}
