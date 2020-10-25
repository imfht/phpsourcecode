<?php
namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;

class Article extends Common
{
    public function index($act=null, $cid=null)
    {
        if ($act=='del') {
            if (!Request::isPost()) {
                return $this->error('参数错误，请重试！');
            }
            $aids = input('post.');
            if ($aids) {
                Db::name('article')->where('aid', 'in', $aids['aids'])->delete();
                addlog('删除文章，AID：'.implode(',', $aids['aids']), $this->user['username']);
                return $this->success('恭喜，删除成功！', url('admin/article/index'));
            }
            return $this->error('请选择需要删除的文章！');
        }

        $cid = intval($cid);
        if ($cid) {
            $where = "a.cid = '{$cid}'";
        } else {
            $where = '1=1';
        }
        $list = Db::name('article')->alias('a')->join('category c', 'a.cid=c.id')->field('a.*,c.name')->where($where)->order('a.aid desc')->paginate(25);
        View::assign('list', $list);
        return View::fetch();
    }

    public function add()
    {
        $category = Db::name('category')->field('id,pid,name')->order('o asc')->select();
        $category = $this->getMenu($category);
        View::assign('category', $category);

        return View::fetch();
    }

    public function edit($aid=null)
    {
        $aid = intval($aid);
        $article = Db::name('article')->where(['aid'=>$aid])->find();
        if (!$article) {
            return $this->error('参数错误，请重试！');
        }
        $category = Db::name('category')->field('id,pid,name')->order('o asc')->select();
        $category = $this->getMenu($category);
        View::assign('category', $category);
        View::assign('article', $article);
        return View::fetch();
    }

    public function save($aid=null)
    {
        if (!Request::isPost()) {
            return $this->error('参数错误，请重试！');
        }
        $aid = intval($aid);
        $cid = input('post.cid', 0, 'intval');
        if (!$cid) {
            return $this->error('参数错误，请重试！');
        }
        $title = input('post.title');
        $keywords = input('post.keywords');
        $description = input('post.description');
        $image = input('post.image');
        $t = time();
        $stick = input('post.stick', 0, 'intval');
        $content = input('post.content');
        if (!$aid) {
            $aid = Db::name('article')->insert(['cid'=>$cid,'title'=>$title,'keywords'=>$keywords,'description'=>$description,'image'=>$image,'t'=>$t,'stick'=>$stick,'content'=>$content], false, true);
            if (!$aid) {
                return $this->error('系统错误，请稍后重试！');
            }
            addlog('新增文章，AID：'.$aid, $this->user['username']);
            return $this->success('恭喜，新增文章成功！', url('admin/article/index'));
        } else {
            Db::name('article')->where(['aid'=>$aid])->update(['cid'=>$cid,'title'=>$title,'keywords'=>$keywords,'description'=>$description,'image'=>$image,'t'=>$t,'stick'=>$stick,'content'=>$content]);
            addlog('编辑文章，AID：'.$aid, $this->user['username']);
            return $this->success('恭喜，文章编辑成功！', url('admin/article/index'));
        }
    }
}
