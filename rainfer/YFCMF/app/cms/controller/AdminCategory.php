<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\cms\controller;

use app\cms\model\Category as CategoryModel;
use app\common\widget\Widget;
use think\Db;
use app\admin\controller\Base;

class AdminCategory extends Base
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * 分类列表
     * @throws \think\Exception
     */
    public function categoryIndex()
    {
        $lang = input('lang', $this->lang);
        $map  = [];
        if ($lang) {
            $map[] = ['lang', '=', $lang];
        }
        $model = new CategoryModel();
        $data = $model->where($map)->order('lang Desc,sort')->select();
        foreach ($data as &$value) {
            $value['add'] = url('categoryAdd', ['id' => $value['id']]);
        }
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '标题', 'field' => 'name'],
            ['title' => '语言', 'field' => 'lang'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => url('categoryState'), 'options' => [0 => '禁用', 1 => '启用']]
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'add'    => ['field' => 'add', 'title' => '添加子分类', 'icon' => 'ace-icon fa fa-plus-circle bigger-130', 'class' => 'blue', 'is_pop' => 1],
            'edit'   => ['href' => url('categoryEdit'), 'is_pop' => 1],
            'delete' => url('categoryDel')
        ];
        $search       = [
            ['select', 'lang', '', ['zh-cn' => '中文', 'en-us' => '英文'], $lang, '', '', ['is_formgroup' => false], 'class' => ''],
        ];
        $form         = [
            'href'  => url('categoryIndex'),
            'class' => 'form-search',
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($fields[3]);
            $form = $search = [];
        }
        $order        = url('categoryOrder');
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, '', $order, '', 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => url('categoryAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, '', $order)
                ->setButton()
                ->fetch();
        }
    }

    /**
     * 分类添加显示
     *
     * @throws \think\Exception
     */
    public function categoryAdd()
    {
        $pid        = input('id', 0);
        $model = new CategoryModel();
        if ($pid) {
            $lang = $model->where('id', $pid)->value('lang');
        } else {
            $lang = '';
        }
        $where = [];
        if (!empty($lang)) {
            $where[] = ['lang', '=', $lang];
        }
        $tpls      = get_tpls('cms');
        $category_text = $model->where($where)->order('lang Desc,sort')->select();
        $category_text = tree_left($category_text, 'id', 'pid');
        $data      = [];
        foreach ($category_text as $value) {
            $data[$value['id']] = $value['lefthtml'] . $value['name'];
        }
        $widget = new Widget();
        $widget = $widget
            ->addSelect('pid', '父级分类', $data, $pid, '', '', ['default' => '顶级'])
            ->addText('name', '分类名', '', '*', 'required', 'text')
            ->addText('enname', '分类英文名', '', '', '', 'text')
            ->addSwitch('status', '是否启用', 0)
            ->addText('sort', '排序', 50, '* 从小到大排序', 'required', 'number')
            ->addSelect('tpl_list', '列表页模板', $tpls, '', '', '', ['default' => '请选择模板'])
            ->addSelect('tpl_detail', '详情页模板', $tpls, '', '', '', ['default' => '请选择模板'])
            ->addText('seo_title', 'SEO标题', '', '', '', 'text')
            ->addText('seo_kwd', 'SEO关键词', '', '', '', 'text')
            ->addText('seo_dec', 'SEO描述', '', '', '', 'text');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '选择语言', ['zh-cn' => '中文', 'en-us' => '英文'], $lang, '', '', ['default' => '请选择语言'])
                ->setTrigger('pid', '', 'lang' . false);
        }
        return $widget->setUrl(url('categorySave'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 分类添加操作
     * @throws
     */
    public function categorySave()
    {
        $name = input('name');
        if (!$name) {
            $this->error('name不为为空', 'categoryIndex');
        }
        $model = new CategoryModel();
        //处理语言
        $pid      = input('pid', 0, 'intval');
        if ($pid) {
            $category_pid = $model->find($pid);
            $lang     = $category_pid['lang'];
        } else {
            $lang = input('lang', $this->lang);
        }
        //构建数组
        $data = [
            'name'       => $name,
            'lang'       => $lang,
            'enname'     => input('enname'),
            'pid'        => $pid,
            'tpl_list'   => input('tpl_list', 'list.html'),
            'tpl_detail' => input('tpl_detail', 'detail.html'),
            'status'     => input('status', 0),
            'sort'       => input('sort'),
            'seo_title'  => input('seo_title'),
            'seo_kwd'    => input('seo_kwd'),
            'seo_dec'    => input('seo_dec')
        ];
        // 启动事务
        Db::startTrans();
        try {
            $model::create($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('分类添加失败', 'categoryIndex', ['is_frame'=>1]);
        }
        $this->success('分类添加成功', 'categoryIndex', ['is_frame'=>1]);
    }

    /**
     * 分类编辑显示
     * @throws
     */
    public function categoryEdit()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $model = new CategoryModel();
        $category       = $model->find($id);
        if (!$category) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $tpls      = get_tpls('cms');
        $category_text = $model->where('lang', $category['lang'])->order('lang Desc,sort')->select();
        $category_text = tree_left($category_text, 'id', 'pid');
        $data      = [];
        foreach ($category_text as $value) {
            $data[$value['id']] = $value['lefthtml'] . $value['name'];
        }

        $widget = new Widget();
        $widget = $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addSelect('pid', '父级分类', $data, $category['pid'], '', '', ['default' => '顶级'])
            ->addText('name', '分类名', $category['name'], '*', 'required', 'text')
            ->addText('enname', '分类英文名', $category['enname'], '', '', 'text')
            ->addSwitch('status', '是否启用', $category['status'])
            ->addText('sort', '排序', $category['sort'], '* 从小到大排序', 'required', 'number')
            ->addSelect('tpl_list', '列表页模板', $tpls, $category['tpl_list'], '', '', ['default' => '请选择模板'])
            ->addSelect('tpl_detail', '详情页模板', $tpls, $category['tpl_detail'], '', '', ['default' => '请选择模板'])
            ->addText('seo_title', 'SEO标题', $category['seo_title'], '', '', 'text')
            ->addText('seo_kwd', 'SEO关键词', $category['seo_kwd'], '', '', 'text')
            ->addText('seo_dec', 'SEO描述', $category['seo_dec'], '', '', 'text');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '选择语言', ['zh-cn' => '中文', 'en-us' => '英文'], $category['lang'], '', '', ['default' => '请选择语言'])
                ->setTrigger('pid', '', 'lang' . false);
        }
        return $widget->setUrl(url('categoryUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 分类编辑操作
     * @throws
     */
    public function categoryUpdate()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $name = input('name');
        if (!$name) {
            $this->error('name不为为空', 'categoryIndex');
        }
        $model = new categoryModel();
        //处理语言
        $pid      = input('pid', 0, 'intval');
        if ($pid) {
            $category_pid = $model->find($pid);
            $lang     = $category_pid['lang'];
        } else {
            $lang = input('lang', $this->lang);
        }
        //构建数组
        $data = [
            'id'         => $id,
            'name'       => $name,
            'lang'       => $lang,
            'enname'     => input('enname'),
            'pid'        => $pid,
            'tpl_list'   => input('tpl_list', 'list.html'),
            'tpl_detail' => input('tpl_detail', 'detail.html'),
            'status'     => input('status', 0),
            'sort'       => input('sort'),
            'seo_title'  => input('seo_title'),
            'seo_kwd'    => input('seo_kwd'),
            'seo_dec'    => input('seo_dec')
        ];
        // 启动事务
        Db::startTrans();
        try {
            $model::update($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('分类编辑失败', 'categoryIndex', ['is_frame'=>1]);
        }
        $this->success('分类编辑成功', 'categoryIndex', ['is_frame'=>1]);
    }

    /**
     * 分类删除
     * @throws
     */
    public function categoryDel()
    {
        $id         = input('id', 0, 'intval');
        $model = new CategoryModel();
        if (!$id) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $category = $model->find($id);
        if (!$category) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $ids = get_category_byid($id, 1, 2);//返回含自身id及子分类id数组
        // 启动事务
        Db::startTrans();
        try {
            categoryModel::destroy($ids);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('分类删除失败', 'categoryIndex');
        }
        $this->success('分类删除成功', 'categoryIndex');
    }

    /**
     * 分类排序
     * @throws
     */
    public function categoryOrder()
    {
        $datas = input('post.');
        $data  = [];
        foreach ($datas as $id => $sort) {
            $data[] = ['id' => $id, 'sort' => $sort];
        }
        $model = new CategoryModel();
        $rst        = $model->saveAll($data);
        if ($rst !== false) {
            $this->success('排序更新成功', 'categoryIndex');
        } else {
            $this->error('排序更新失败', 'categoryIndex');
        }
    }

    /**
     * 分类启用禁用
     */
    public function categoryState()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            $this->error('分类不存在', 'categoryIndex');
        }
        $model = new CategoryModel();
        $status = $model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $model->where('id', $id)->setField('status', $status);
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }
}
