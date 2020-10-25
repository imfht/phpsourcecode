<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;

class Menu extends Common
{
    public function index($act=null)
    {
        if ($act=='del') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $ids = input('post.');

            if (!empty($ids)) {
                $r = Db::name('menu')->delete($ids['ids']);
                if ($r) {
                    addlog('删除菜单，ID：'.implode(',', $ids['ids']), $this->user['username']);
                    return $this->success('恭喜，菜单删除成功！', url('admin/menu/index'));
                }
            }

            return $this->error('请选择需要删除的选项！');
        }

        if ($act=='update') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $id = input('post.id', 0, 'intval');
            $pid = input('post.pid', 0, 'intval');
            $url = input('post.url');
            $title = input('post.title');
            $icon = input('post.icon');
            $tips = input('post.tips');
            $status = input('post.status', 0, 'intval');
            $o = input('post.o', 0, 'intval');

            if ($id==0) {//新增
                $id = Db::name('menu')->insert(['pid'=>$pid,'url'=>$url,'title'=>$title,'icon'=>$icon,'tips'=>$tips,'status'=>$status,'o'=>$o], false, true);
                if ($id) {
                    addlog('新增菜单，ID:'.$id, $this->user['username']);
                    return $this->success('恭喜，新增菜单成功！', url('admin/menu/index'));
                }
            } else {//编辑
                Db::name('menu')->where('id', $id)->update(['pid'=>$pid,'url'=>$url,'title'=>$title,'icon'=>$icon,'tips'=>$tips,'status'=>$status,'o'=>$o]);
                addlog('编辑菜单，ID:'.$id, $this->user['username']);
                return $this->success('恭喜，编辑菜单成功！', url('admin/menu/index'));
            }

            return $this->error('系统错误，请稍后再试！');
        }

        if ($act=='edit') {
            $id = Request::param('id');
            View::assign('id', $id);

            $current = Db::name('menu')->where(['id'=>$id])->find();
            if (!$current) {
                return $this->error('参数错误，请重试！');
            }
            View::assign('current', $current);

            $list = Db::name('menu')->field('id,pid,title')->where(['status'=>1])->order('o ASC')->select();
            $list = $this->getMenu($list);
            View::assign('list', $list);

            return View::fetch('form');
        }

        if ($act=='add') {
            $list = Db::name('menu')->field('id,pid,title')->where(['status'=>1])->order('o ASC')->select();
            $list = $this->getMenu($list);
            View::assign('list', $list);

            return View::fetch('form');
        }

        $list = Db::name('menu')->field('id,pid,url,title,icon,status,o')->order('o ASC')->paginate(25);
        View::assign('list', $list);
        return View::fetch();
    }
}
