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
namespace addons\nav\controller;

use app\common\controller\Base;
use app\common\model\Addon as AddonModel;
use app\common\widget\Widget;
use addons\nav\model\Menu as MenuModel;
use think\Db;

class Admin extends Base
{
    protected function initialize()
    {
        //调用admin/Base控制器的初始化
        action('admin/Base/initialize');
        $this->lang = $this->lang ? : 'zh-cn';
    }

    /**
     * 前台菜单列表
     * @throws \think\Exception
     */
    public function menuIndex()
    {
        $lang = input('lang', $this->lang);
        $map  = [];
        if ($lang) {
            $map[] = ['lang', '=', $lang];
        }
        $menu_model = new MenuModel();
        $data = $menu_model->where($map)->order('lang Desc,sort')->select();
        foreach ($data as &$value) {
            $value['add'] = addon_url('nav://Admin/menuAdd', ['id' => $value['id']]);
        }
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '类型', 'field' => 'type', 'type' => 'array', 'array' => [1 => '单页', 2 => '列表', 3 => '链接']],
            ['title' => '标题', 'field' => 'name'],
            ['title' => '语言', 'field' => 'lang'],
            ['title' => '状态', 'field' => 'status', 'type' => 'switch', 'url' => addon_url('nav://Admin/menuState'), 'options' => [0 => '隐藏', 1 => '显示']]
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
            'add'    => ['field' => 'add', 'title' => '添加子菜单', 'icon' => 'ace-icon fa fa-plus-circle bigger-130', 'class' => 'blue', 'is_pop' => 1],
            'edit'   => ['href' => addon_url('nav://Admin/menuEdit'), 'is_pop' => 1],
            'delete' => addon_url('nav://Admin/menuDel')
        ];
        $search       = [
            ['select', 'lang', '', ['zh-cn' => '中文', 'en-us' => '英文'], $lang, '', '', ['is_formgroup' => false], 'class' => 'ajax_change'],
        ];
        $form         = [
            'href'  => addon_url('nav://Admin/menuIndex'),
            'class' => 'form-search',
        ];
        if (!config('yfcmf.lang_switch_on')) {
            unset($fields[4]);
            $form = $search = [];
        }
        $order        = addon_url('nav://Admin/menuOrder');
        //实例化表单类
        $widget = new Widget();
        if (request()->isAjax()) {
            return $widget
                ->form('table', $fields, $pk, $data, $right_action, '', $order, '', 1);
        } else {
            return $widget
                ->addToparea(['add' => ['href' => addon_url('nav://Admin/menuAdd'), 'is_pop' => 1]], [], $search, $form)
                ->addtable($fields, $pk, $data, $right_action, '', $order)
                ->setButton()
                ->fetch();
        }
    }
    /**
     * 前台菜单添加显示
     *
     * @throws \think\Exception
     */
    public function menuAdd()
    {
        $pid        = input('id', 0);
        $menu_model = new MenuModel();
        if ($pid) {
            $lang = $menu_model->where('id', $pid)->value('lang');
        } else {
            $lang = '';
        }
        $where = [];
        if (!empty($lang)) {
            $where[] = ['lang', '=', $lang];
        }
        $menu_text = $menu_model->where($where)->order('lang Desc,sort')->select();
        $menu_text = tree_left($menu_text, 'id', 'pid');
        $data      = [];
        foreach ($menu_text as $value) {
            $data[$value['id']] = $value['lefthtml'] . $value['name'];
        }
        $widget = new Widget();
        $widget = $widget
            ->addSelect('pid', '父级菜单', $data, $pid, '', '', ['default' => '顶级'])
            ->addText('url', '链接地址', '', '外链或单页地址或列表地址', '', 'text')
            ->addSelect('target', '打开方式', ['_self'=>'本窗口', '_blank'=>'新窗口'], '_self')
            ->addText('name', '菜单名', '', '*', 'required', 'text')
            ->addText('enname', '菜单英文名', '', '', '', 'text')
            ->addSwitch('status', '是否启用', 0)
            ->addText('sort', '排序', 50, '* 从小到大排序', 'required', 'number')
            ->addText('seo_title', 'SEO标题', '', '', '', 'text')
            ->addText('seo_kwd', 'SEO关键词', '', '', '', 'text')
            ->addText('seo_dec', 'SEO描述', '', '', '', 'text');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '选择语言', ['zh-cn' => '中文', 'en-us' => '英文'], $lang, '', '', ['default' => '请选择语言'])
                ->setTrigger('pid', '', 'lang' . false);
        }
        return $widget->setUrl(addon_url('nav://Admin/menuSave'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }
    /**
     * 前台菜单添加操作
     * @throws \think\Exception
     */
    public function menuSave()
    {
        $name = input('name');
        if (!$name) {
            $this->error('name不为为空', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
        }
        $menu_model = new MenuModel();
        //处理语言
        $pid      = input('pid', 0, 'intval');
        if ($pid) {
            $menu_pid = $menu_model->find($pid);
            $lang     = $menu_pid['lang'];
        } else {
            $lang = input('lang', $this->lang);
        }
        //构建数组
        $data = [
            'name'       => $name,
            'lang'       => $lang ? : 'zh-cn',
            'enname'     => input('enname'),
            'pid'        => $pid,
            'url'        => input('url'),
            'target'        => input('target', '_self'),
            'status'     => input('status', 0),
            'sort'       => input('sort'),
            'seo_title'  => input('seo_title'),
            'seo_kwd'    => input('seo_kwd'),
            'seo_dec'    => input('seo_dec')
        ];
        // 启动事务
        Db::startTrans();
        try {
            $menu_model::create($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('菜单添加失败', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
        }
        $this->success('菜单添加成功', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
    }
    /**
     * 前台菜单编辑显示
     * @throws \think\Exception
     */
    public function menuEdit()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'));
        }
        $menu_model = new MenuModel();
        $menu       = $menu_model->find($id);
        if (!$menu) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'));
        }
        $menu_text = $menu_model->where('lang', $menu['lang'])->order('lang Desc,sort')->select();
        $menu_text = tree_left($menu_text, 'id', 'pid');
        $data      = [];
        foreach ($menu_text as $value) {
            $data[$value['id']] = $value['lefthtml'] . $value['name'];
        }

        $widget = new Widget();
        $widget = $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addSelect('pid', '父级菜单', $data, $menu['pid'], '', '', ['default' => '顶级'])
            ->addText('url', '链接地址', $menu['url'], '外链或单页地址', '', 'text')
            ->addSelect('target', '打开方式', ['_self'=>'本窗口', '_blank'=>'新窗口'], $menu['target'])
            ->addText('name', '菜单名', $menu['name'], '*', 'required', 'text')
            ->addText('enname', '菜单英文名', $menu['enname'], '', '', 'text')
            ->addSwitch('status', '是否启用', $menu['status'])
            ->addText('sort', '排序', $menu['sort'], '* 从小到大排序', 'required', 'number')
            ->addText('seo_title', 'SEO标题', $menu['seo_title'], '', '', 'text')
            ->addText('seo_kwd', 'SEO关键词', $menu['seo_kwd'], '', '', 'text')
            ->addText('seo_dec', 'SEO描述', $menu['seo_dec'], '', '', 'text');
        if (config('yfcmf.lang_switch_on')) {
            $widget = $widget->addSelect('lang', '选择语言', ['zh-cn' => '中文', 'en-us' => '英文'], $menu['lang'], '', '', ['default' => '请选择语言'])
                ->setTrigger('pid', '', 'lang' . false);
        }
        return $widget->setUrl(addon_url('nav://Admin/menuUpdate'))
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }
    /**
     * 前台菜单编辑操作
     * @throws
     */
    public function menuUpdate()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
        }
        $name = input('name');
        if (!$name) {
            $this->error('name不为为空', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
        }
        $menu_model = new MenuModel();
        //处理语言
        $pid      = input('pid', 0, 'intval');
        if ($pid) {
            $menu_pid = $menu_model->find($pid);
            $lang     = $menu_pid['lang'];
        } else {
            $lang = input('lang', $this->lang);
        }
        //构建数组
        $data = [
            'id'         => $id,
            'name'       => $name,
            'lang'       => $lang ? : 'zh-cn',
            'enname'     => input('enname'),
            'pid'        => $pid,
            'url'        => input('url'),
            'target'        => input('target', '_self'),
            'status'     => input('status', 0),
            'sort'       => input('sort'),
            'seo_title'  => input('seo_title'),
            'seo_kwd'    => input('seo_kwd'),
            'seo_dec'    => input('seo_dec')
        ];
        // 启动事务
        Db::startTrans();
        try {
            $menu_model::update($data);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('菜单编辑失败', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
        }
        $this->success('菜单编辑成功', addon_url('nav://Admin/menuIndex'), ['is_frame'=>1]);
    }
    /**
     * 前台菜单删除
     * @throws
     */
    public function menuDel()
    {
        $id         = input('id', 0, 'intval');
        $menu_model = new MenuModel();
        if (!$id) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'));
        }
        $menu = $menu_model->find($id);
        if (!$menu) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'));
        }
        $ids = $menu_model->get_menu_byid($id, 1, 2);//返回含自身id及子菜单id数组
        // 启动事务
        Db::startTrans();
        try {
            MenuModel::destroy($ids);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->error('菜单删除失败', addon_url('nav://Admin/menuIndex'));
        }
        $this->success('菜单删除成功', addon_url('nav://Admin/menuIndex'));
    }
    /**
     * 前台菜单排序
     * @throws
     */
    public function menuOrder()
    {
        $datas = input('post.');
        $data  = [];
        foreach ($datas as $id => $sort) {
            $data[] = ['id' => $id, 'sort' => $sort];
        }
        $menu_model = new MenuModel();
        $rst        = $menu_model->saveAll($data);
        if ($rst !== false) {
            $this->success('排序更新成功', addon_url('nav://Admin/menuIndex'));
        } else {
            $this->error('排序更新失败', addon_url('nav://Admin/menuIndex'));
        }
    }
    /**
     * 前台菜单开启/禁止
     */
    public function menuState()
    {
        $id = input('id', 0, 'intval');
        if (!$id) {
            $this->error('菜单不存在', addon_url('nav://Admin/menuIndex'));
        }
        $menu_model = new MenuModel();
        $status = $menu_model->where('id', $id)->value('status');
        $status = $status ? 0 : 1;
        $menu_model->where('id', $id)->setField('status', $status);
        cache('site_nav_main', null);
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }
}
