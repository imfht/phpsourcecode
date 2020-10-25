<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/10/10
 * Time: 14:14
 */

namespace app\admin\controller;

use app\common\model\Article;
use think\Db;

class ArticleController extends BaseController
{

    public function initialize()
    {
        parent::initialize();
        $cate = Article::$cate;
        $this->assign('cate', $cate);
    }

    public function index()
    {
        if ($this->request->isAjax()) {
            $keyword = $this->request->post('keyword', '', 'trim');
            $model = Db::name('article')->where('status', 'in', '0,1');
            if ($keyword) {
                $model->where('title', 'like', '%' . $keyword . '%');
            }
            $list = $model->order('id desc')->paginate(10);
            return view('index_list', [
                'list' => $list,
                'cate' => Article::$cate,
            ]);
        } else {
            return view();
        }
    }

    /**
     * 添加页面和添加操作
     * @return mixed
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $Article         = new Article();
            $post            = $this->request->post();
            $post['cate']    = 1;
            $post['content'] = $this->request->post('content', '', null);
            if ($Article->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('新增成功', url('index'));
        } else {
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
            $Article         = new Article();
            $post            = $this->request->post();
            $post['content'] = $this->request->post('content', '', null);
            if ($Article->isUpdate(true)->allowField(true)->save($post) === false) {
                $this->error($Article->getError());
            }
            $this->success('修改成功', url('index'));
        } else {
            $id = $this->request->get('id', 0, 'intval');
            if (!$id) {
                $this->error('文章不存在');
            }
            $info = Article::get($id);
            $this->assign('info', $info);
            $this->assign('cate', Article::$cate);
            return view();
        }
    }

    /**
     * 设置状态
     * @return json
     */
    public function setStatus()
    {
        $id     = $this->request->get('id', 0, 'intval');
        $status = $this->request->get('status', 0, 'intval');

        if ($id > 0 && (new Article())->where('id', $id)->update(['status' => $status]) !== false) {
            $this->success('设置成功');
        }
        $this->error('更新失败');
    }

    //推送
    public function push()
    {
        $id   = $this->request->get('id', 0, 'intval');
        $info = Db::name('article')->find($id);
        if (!$info || $info['status'] != 1) {
            $this->error('该文章状态暂时无法推送');
        }
        Push::alert($info['title'], 0, ['articleId' => $id, 'pId' => '101', 'url' => $this->request->domain() . '/view/' . $id]);
        $this->success('推送成功');
    }


    /**
     * 删除
     */
    public function delete()
    {
        $id = $this->request->get('id', 0, 'intval');
        if ($id > 0 && Db::name('article')->where('id', $id)->where('cate', 'neq', 1)->setField('status', 2) !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

}