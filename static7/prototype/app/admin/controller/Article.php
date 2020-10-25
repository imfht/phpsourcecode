<?php

namespace app\admin\controller;

use think\Loader;
use think\Url;
use think\Request;
use think\Db;
use think\Session;

/**
 * Description of Article
 * 内容管理
 * @author static7
 */
class Article extends Admin {

    //文章通用条件
    protected $currentField = 'id,uid,title,category_id,create_time,update_time,type,view,status';

    /**
     * 内容列表
     * @param int $cate_id 分类id
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function index($cate_id = 0) {
        $map = [
            'category_id' => (int) $cate_id ?? ['gt', 0],
            'uid' => $this->uid,
            'status' => ['neq', -1]
        ];
        $data = Loader::model('Document')->lists($map, $this->currentField, 'create_time DESC');
        $data['data'] = $this->categoryTitle($data['data']);
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'cate_id' => $cate_id ?? 0,
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '文章列表';
        return $this->view->assign($value)->fetch();
    }

    /**
     * ajax分页
     * @param int $cate_id 分类id
     * @param array $condition 接受参数临时变量
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function ajaxIndex() {
        $Document = Loader::model('Document');
        $condition_tmp = Request::instance()->param();
        $map = [
            'category_id' => (int) $condition_tmp['cate_id'] ?? ['gt', 0],
            'uid' => $this->uid,
            'status' => ['neq', -1]
        ];
        if (!empty($condition_tmp)) {
            $condition = Loader::model('Condition', 'logic')->transform($condition_tmp); //处理条件
            $map = array_merge($map, $condition); //合并
        }
        $data = $Document->lists($map, $this->currentField, 'create_time DESC', $condition_tmp);
        $data['data'] = $this->categoryTitle($data['data']);
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'cate_id' => $condition_tmp['cate_id'] ?? 0,
        ];
        return $this->view->assign($value)->fetch('ajax_article');
    }

    /**
     * 移动
     * @param int $cate_id 分类id
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function move($cate_id = 0) {
        (int) $cate_id || $this->error('参数错误');
        $ids = Request::instance()->post();
        empty($ids['ids']) && $this->error('请选择要移动的文章！');
        Session::set('moveArticle', $ids['ids'], 'move_article');
        Session::delete('copyArticle', 'copy_article');
        return $this->success('请选择要移动到的分类！', '');
    }

    /**
     * 复制
     * @param int $cate_id 分类id
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function copy($cate_id = 0) {
        (int) $cate_id || $this->error('参数错误');
        $ids = Request::instance()->post();
        empty($ids['ids']) && $this->error('请选择要复制的文章！');
        Session::set('copyArticle', $ids['ids'], 'copy_article');
        Session::delete('moveArticle', 'move_article');
        return $this->success('请选择要复制到的分类！', '');
    }

    /**
     * 粘贴
     * @param int $cate_id 分类id
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function paste($cate_id = 0) {
        (int) $cate_id || $this->error('请选择要粘贴到的分类!');
        $moveList = Session::get('moveArticle', 'move_article');
        $copyList = Session::get('copyArticle', 'copy_article');
        if (empty($moveList) && empty($copyList)) {
            return $this->error('没有选择文档！');
        }
        $list_id = null;
        if ($moveList) {
            $list_id = $this->moveList($moveList, $cate_id, $list_id);
            return empty($list_id) ? $this->success('文章移动成功！') : $this->success("文章编号{$list_id}移动失败！请重新选择移动");
        } elseif ($copyList) {
            $list_id = $this->copyList($copyList, $cate_id, $list_id);
            return empty($list_id) ? $this->success('文章复制成功！') : $this->success("文章编号{$list_id}复制失败！请重新选择复制");
        }
        return $this->error('文章获取失败');
    }

    /**
     * 移动文章
     * @param array $moveList 文章id数组
     * @param int $cate_id 分类id
     * @param null $list_id 失败id
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function moveList($moveList = null, $cate_id = 0, $list_id = null) {
        $Document = Db::name('Document');
        foreach ($moveList as $key => $value) {
            $res = $Document->update(['id' => $value, 'category_id' => $cate_id]);
            if (false === $res) {
                $list_id .= $value . ',';
                continue;
            }
        }
        empty($list_id) && Session::delete('moveArticle', 'move_article');
        return $list_id;
    }

    /**
     * 复制文章
     * @param array $copyList 文章id数组
     * @param int $cate_id 分类id
     * @param null $list_id 失败id
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function copyList($copyList = null, $cate_id = 0, $list_id = null) {
        $Document = Db::name('Document');
        $time = Request::instance()->time();
        $DocumentArticle = Loader::model('DocumentArticle', 'logic');
        foreach ($copyList as $key => $value) {
            $data = $Document->where('id', $value)->find();
            unset($data['id']);
            unset($data['name']);
            $data['category_id'] = $cate_id;
            $data['create_time'] = $time;
            $data['update_time'] = $time;
            $result_id = $Document->insertGetId($data);
            if (!$result_id) {
                $list_id .= $value . ',';
                continue;
            }
            unset($data);
            $article = $DocumentArticle->detail($value); //获取指定ID的扩展数据
            if ($DocumentArticle->getError()) {
                $Document->delete($value);
                $list_id .= $value . ',';
                continue;
            }
            $data = $article->toArray();
            $data['id'] = $result_id;
            $article_id = $DocumentArticle->renew($data);
            if (false === $article_id) {
                $list_id .= $value . ',';
                continue;
            }
        }
        empty($list_id) && Session::delete('copyArticle', 'copy_article');
        return $list_id;
    }

    /**
     * 我的文章
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function mydocument() {
        $map = [
            'status' => ['in', '0,1,2'],
            'uid' => $this->uid
        ];
        $data = Loader::model('Document')->lists($map, $this->currentField, 'create_time DESC');
        $data['data'] = $this->categoryTitle($data['data']);
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '我的文章';
        return $this->view->assign($value)->fetch();
    }

    /**
     * ajax分页
     * @param array $condition 接受参数临时变量
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function ajaxMyDocument() {
        $Document = Loader::model('Document');
        $map = [
            'status' => ['in', '0,1,2'],
            'uid' => $this->uid
        ];
        $condition_tmp = Request::instance()->param();
        if (!empty($condition_tmp)) {
            $condition = Loader::model('Condition', 'logic')->transform($condition_tmp); //处理条件
            $map = array_merge($map, $condition);
        }
        $data = $Document->lists($map, $this->currentField, 'create_time DESC', $condition_tmp);
        $data['data'] = $this->categoryTitle($data['data']);
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
        ];
        return $this->view->assign($value)->fetch('ajax_article');
    }

    /**
     * 草稿箱
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function draftbox() {
        $map = [
            'status' => 3,
            'uid' => $this->uid
        ];
        $data = Loader::model('Document')->lists($map, $this->currentField, 'update_time DESC');
        $data['data'] = $this->categoryTitle($data['data']);
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '草稿箱';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 待审核
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function examine() {
        $map = [
            'status' => 2,
            'uid' => $this->uid
        ];
        $data = Loader::model('Document')->lists($map, $this->currentField, 'create_time DESC');
        $data['data'] = $this->categoryTitle($data['data']);
        if (is_array($data['data'])) {//处理列表数据
            foreach ($data['data'] as $k => &$v) {
                $v['username'] = get_nickname($v['uid']);
            }
        }
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '待审核';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 回收站
     * @author staitc7 <static7@qq.com>
     */
    public function recycle() {
        $map = [
            'status' => -1,
            'uid' => $this->uid
        ];
        $data = Loader::model('Document')->lists($map, $this->currentField, 'create_time DESC');
        $data['data'] = $this->categoryTitle($data['data']);
        if (is_array($data['data'])) {//处理列表数据
            foreach ($data['data'] as $k => &$v) {
                $v['username'] = get_nickname($v['uid']);
            }
        }
        $value = [
            'list' => $data['data'] ?? null,
            'page' => $data['page'],
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '回收站';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 文章详情 
     * @param int $id 文章id
     * @param int $cate_id 分类id
     * @author staitc7 <static7@qq.com>
     */
    public function add($cate_id = 0) {
        (int) $cate_id || $this->error('参数错误');
        $value = [
            'category_id' => $cate_id,
            'child' => $this->categoryMenu()
        ];
        $this->view->metaTitle = '添加文章';
        return $this->view->assign($value ?? null)->fetch('edit');
    }

    /**
     * 文章详情 
     * @param int $id 文章id
     * @author staitc7 <static7@qq.com>
     */
    public function edit($id = 0, $cate_id = 0, $model_id = 0) {
        (int) $cate_id || $this->error('参数错误');
        $value = [
            'category_id' => $cate_id,
            'model_id' => (int) $model_id ?? 0,
            'child' => $this->categoryMenu()
        ];
        if ((int) $id > 0) {
            $Document = Loader::model('Document');
            $info = $Document->detail((int) $id);
            $Document->getError() && $this->error($Document->getError()); //返回错误
            $value ['info'] = $info ?? null;
        }
        $this->view->metaTitle = '编辑文章';
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 通用单条数据状态修改
     * @param int $value 状态
     * @param int ids 数据条件
     * @author staitc7 <static7@qq.com>
     */
    public function setStatus(Request $Request, $value = null, $ids = null) {
        empty($ids) && $this->error('请选择要操作的数据');
        !is_numeric((int) $value) && $this->error('参数错误');
        $data ['status'] = $value;
        ((int) $value !== -1) || $data['update_time'] = $Request->time();
        $info = Loader::model('Document')->setStatus(['id' => ['in', $ids]], $data);
        return $info !== FALSE ?
                $this->success($value == -1 ? '删除成功' : '更新成功') :
                $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 通用批量数据更新
     * @param int $value 状态
     * @author staitc7 <static7@qq.com>
     */
    public function batchUpdate(Request $Request, $value = null) {
        $ids = $Request->post();
        empty($ids['ids']) && $this->error('请选择要操作的数据');
        !is_numeric((int) $value) && $this->error('参数错误');
        $data ['status'] = $value;
        ((int) $value !== -1) || $data['update_time'] = $Request->time();
        $info = Loader::model('Document')->setStatus(['id' => ['in', $ids['ids']]], $data);
        return $info !== FALSE ? $this->success($value == -1 ? '删除成功' : '更新成功') : $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 文章封面
     * @author staitc7 <static7@qq.com>
     */
    public function articlePicture() {
        $info = Loader::model('Picture', 'api')->upload('articlePicture');
        return is_numeric($info) ? $this->success('上传成功!', '', $info) : $this->error($info);
    }

    /**
     * 文章附件
     * @author staitc7 <static7@qq.com>
     */
    public function articleFile() {
        $info = Loader::model('File', 'api')->upload('file');
        return is_numeric($info) ? $this->success('上传成功!', '', $info) : $this->error($info);
    }

    /**
     * 编辑图片
     * @author staitc7 <static7@qq.com> 
     */
    public function editorPicture() {
        $Picture = Loader::model('File', 'api');
        $info = $Picture->editorUpload('imgFile');
        /* 记录附件信息 */
        if ($Picture->getError()) {
            $return['error'] = 1;
            $return['message'] = $Picture->getError();
        } else {
            $return['error'] = 0;
            $return['url'] = $info;
        }
        /* 返回JSON数据 */
        exit(json_encode($return));
    }

    /**
     * 用户更新或者添加文章
     * @author staitc7 <static7@qq.com>
     */
    public function renew() {
        $Document = Loader::model('Document');
        $info = $Document->renew();
        if ($Document->getError()) {
            return $this->error($Document->getError());
        } else {
            return $this->success('操作成功', Url::build('index', ['cate_id' => $info['category_id']]));
        }
    }

    /**
     * 草稿-自动保存
     * @author staitc7 <static7@qq.com>
     */
    public function autoSave() {
        $data = Request::instance()->post();
        $validate = Loader::validate('Document');
        $validate->extend([
            'checkCategory' => function($value) {
                return checkCategory((int) $value, 'allow_publish') ? true : '该分类不允许发布内容';
            },
        ]);
        !$validate->check($data) && $this->error($validate->getError()); // 验证失败 输出错误信息
        $article = null;
        foreach ($data as $k => &$v) {
            if (in_array($k, ['parse', 'content', 'template', 'bookmark', 'keywords', 'file_id', 'download', 'size'])) {
                $article[$k] = $v;
                unset($data[$k]);
            }
        }
        $data['status'] = 3;
        $data['uid'] = $this->uid;
        $data['update_time'] = Request::instance()->time();
        $Document = Db::name('Document');
        $id = (int) $data['id'] ? $Document->update($data) : $Document->insertGetId($data);
        if ($id === false) {
            return $this->error('未知错误！');
        }
        $article['id'] = (int) $data['id'] ? $data['id'] : $id;
        $DocumentArticle = new \app\admin\logic\DocumentArticle();
        $content = $DocumentArticle->renew($article);
        if ($DocumentArticle->getError()) {
            $data['id'] || $Document->delete($data['id']); //新增失败，删除基础数据
            return $this->error($DocumentArticle->getError());
        }
        return $content ? $this->success('草稿保存成功', null, $article['id']) : $this->error('未知错误！');
    }

    /**
     * 处理数据分类
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    private function categoryTitle(array $data = []) {
        if (is_array($data)) {//处理列表数据
            $Category = Db::name('Category');
            foreach ($data as $k => &$v) {
                $v['category_text'] = $Category->where('id', $v['category_id'])->value('title');
            }
        }
        return $data;
    }

    /**
     * 分类菜单
     * @author staitc7 <static7@qq.com>
     */
    public function categoryMenu() {
        $Request = Request::instance();
        $cate = Session::get('admin_category_menu.', 'category_menu');
        if (empty($cate)) {
            $cate_tmp = Db::name('Category')->where(['status' => 1, 'level' => ['elt', 3]])->field('id,title,pid,allow_publish')->order('pid DESC,sort DESC')->select();
            $cate = list_to_tree($cate_tmp); //生成分类树
            Session::set('admin_category_menu.', $cate, 'category_menu');
        }
        $cate_id = $Request->param('cate_id') ?? 0;
        //是否展开分类
        $hide_cate = !in_array(strtolower($Request->action()), ['recycle', 'examine', 'draftbox', 'mydocument']) ? true : false;
        foreach ($cate as $key => &$value) {//生成每个分类的url
            $value['url'] = Url::build('Article/index', ['cate_id' => $value['id']]);
            $value['active'] = ((int) $cate_id == (int) $value['id'] && $hide_cate) ? true : false;
            if (empty($value['_child'])) {
                continue;
            }
            $is_child = false;
            foreach ($value['_child'] as $ka => &$va) {
                $va['url'] = Url::build('Article/index', ['cate_id' => $va['id']]);
                if (!empty($va['_child'])) {
                    foreach ($va['_child'] as $k => &$v) {
                        $v['url'] = Url::build('Article/index', ['cate_id' => $v['id']]);
                        $v['pid'] = $va['id'];
                        $is_child = (int) $v['id'] == (int) $cate_id ? true : false;
                    }
                }
                if ((int) $va['id'] == (int) $cate_id || (bool) $is_child) {
                    $is_child = false;
                    if ($hide_cate) {
                        $value['active'] = true;
                        $va['active'] = true;
                    } else {
                        $value['active'] = false;
                        $va['active'] = false;
                    }
                } else {
                    $va['active'] = false; //展开子分类的父分类
                }
            }
        }
        return $cate;
    }

    /**
     * 物理删除
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function physicalDelete() {
        $ids = Request::instance()->post();
        empty($ids['ids']) && $this->error('请选择要操作的数据');
        $info = Db::name('Document')->delete($ids['ids']);
        if ($info) {
            Db::name('DocumentArticle')->delete($ids['ids']);
        }
        return $info !== FALSE ? $this->success('删除成功') : $this->error('删除失败');
    }

}
