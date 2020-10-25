<?php

namespace app\admin\controller;

use think\Loader;
use think\Session;
use think\Db;
use think\Url;
use think\Config;

/**
 * Description of Category
 * 分类管理
 * @author static7
 */
class Category extends Admin {

    /**
     * 分类管理首页
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        $tree = Loader::model('Category')->getTree(0, 'id,name,title,sort,pid,allow_publish,status');
        $value = [
            'tree' => $tree
        ];
        Config::set('_system_get_category_true_', true);
        $this->view->metaTitle = '文章列表';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 显示分类树，仅支持内部调
     * @param  array $tree 分类树
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function tree($tree = null) {
        Config::get('_system_get_category_true_') || header("HTTP/1.0 404 Not Found");
        $value = [
            'tree' => $tree
        ];
        return $this->view->assign($value ?? null)->fetch('tree');
    }

    /**
     * 新增分类
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function add($pid = 0) {
        $info = Loader::model('Category')->info((int) $pid, 'id,name,title,level');
        $value = [
            'category' => $info
        ];
        $this->view->metaTitle = '新增分类';
        return $this->view->assign($value ?? null)->fetch('edit');
    }

    /**
     * 分类更新或者添加
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function renew() {
        $info = Loader::model('Category')->renew();
        if (is_array($info)) {
            Session::delete('admin_category_menu', 'category_menu');
            return $this->success('操作成功', Url::build('index'));
        } else {
            return $this->error($info);
        }
    }

    /**
     * 编辑分类
     * @param int $id 分类id
     * @author staitc7 <static7@qq.com>
     */
    public function edit($id = 0) {
        (int) $id || $this->error('分类id错误');
        $value = Loader::model('Category')->edit((int) $id);
        $this->view->metaTitle = '编辑分类';
        return $this->view->assign($value ?? null)->fetch('edit');
    }

    /**
     * 分类删除
     * @param int $id 分类id
     * @author staitc7 <static7@qq.com>
     */
    public function remove($id = 0) {
        (int) $id || $this->error('分类id错误');
        $Category = Loader::model('Category');
        $category = $Category->where(['pid' => $id, 'status' => ['neq', -1]])->column('id');
        if ($category) {
            return $this->error('请先删除该分类下的子分类');
        }
        $document = Db::name('Document')->where(['category_id' => $id])->column('id');
        if ($document) {
            return $this->error('请先删除该分类下的文章（包含回收站）');
        }
        $info = $Category->setStatus(['id' => ['in', $id]], ['status' => -1]);
        if ($info !== FALSE) {
            Session::delete('admin_category_menu', 'category_menu');
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /**
     * 移动分类
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function move($id = 0) {
        (int) $id || $this->error('分类id错误');
        $Category = Db::name('Category');
        $level = $Category->where(['status' => 1, 'id' => ['eq', $id]])->value('level'); //检查等级
        //获取分类
        $map = ['status' => 1, 'id' => ['neq', $id]];
        $level ? $map['level'] = ['elt', $level - 1] : $map['level'] = ['lt', 3];
        $list = $Category->where($map)->field('id,pid,title')->select();
        array_unshift($list, ['id' => 0, 'title' => '根分类']);
        $value = [
            'id' => $id,
            'list' => $list ?? null
        ];
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 更新移动分类
     * @param int $id 分类id
     * @param int $pid 分类id的新父级id  
     * @author staitc7 <static7@qq.com>
     */
    public function moveRenew($id = 0, $pid = 0) {
        (int) $id || $this->error('分类id错误');
        (int) $pid || $this->error('参数错误');
        $status = Db::name('Category')->where('id', $id)->setField('pid', $pid);
        if ($status !== FALSE) {
            Session::delete('admin_category_menu', 'category_menu');
            return $this->success('移动成功');
        } else {
            return $this->error('移动失败');
        }
    }

    /**
     * 分类图片
     * @author staitc7 <static7@qq.com>
     */
    public function categoryPicture() {
        $info = Loader::model('Picture', 'api')->upload('categoryPicture');
        return is_numeric($info) ? $this->success('上传成功!', '', $info) : $this->error($info);
    }

}
