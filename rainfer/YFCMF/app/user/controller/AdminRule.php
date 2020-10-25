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
namespace app\user\controller;

use app\user\model\RoleRule as RoleRuleModel;
use app\common\widget\Widget;
use think\facade\Cache;
use app\common\model\Module as ModuleModel;
use app\admin\controller\Base;
use think\facade\Env;

class AdminRule extends Base
{
    protected $view_path = '';

    public function initialize()
    {
        parent::initialize();
        $this->view->config('view_path',Env::get('app_path').'admin/view/');
    }

    /**
     * 规则列表
     */
    public function ruleIndex()
    {
        $rule_model     = new RoleRuleModel();
        $pid            = input('pid', 0);
        $id             = input('id', 'pid');
        //pid=0菜单
        $admin_rule     = $rule_model->getChilds($pid, 2);
        //全部启用状态菜单tree_left后的数组
        $admin_rule_all = $rule_model->getAll(2, '', 1);
        //全部模块
        $module_model = new ModuleModel();
        $modules = $module_model->getModule();
        $this->assign('modules', $modules);
        $this->assign('admin_rule', $admin_rule);
        $this->assign('admin_rule_all', $admin_rule_all);
        $this->assign('pid', $id);
        if (request()->isAjax()) {
            return $this->fetch('ajax_rule_index');
        } else {
            return $this->view->config('view_path', Env::get('app_path').request()->module().'/view/admin/')->fetch();
        }
    }

    /**
     * 规则添加
     */
    public function ruleAdd()
    {
        $pid = input('id', 0);
        //全部启用菜单
        $rule_model = new RoleRuleModel();
        $data       = $rule_model->getAll(3, '', 1);
        //全部模块
        $module_model = new ModuleModel();
        $modules = $module_model->getModule();
        $widget     = new Widget();
        return $widget
            ->addSelect('pid', '父级权限', $data, $pid, '', '', ['default' => '顶级'])
            ->addSelect('module', '所属模块', $modules, 'admin', '', '', ['default' => '选择模块'])
            ->addText('title', '标题', '', '*', 'required', 'text')
            ->addText('name', '模块/控制器/方法', '', '* 不是实际方法时,使用default方法代替', 'required', 'text')
            ->addText('condition', '附加条件', '', '', '', 'text', ['placeholder'=>'{score}>5  and {score}<100'])
            ->addicon('icon', '图标', '', '只针对顶级栏目有效,例如：fa fa-tachometer')
            ->addText('sort', '排序', 50, '* 从小到大排序', 'required', 'number')
            ->addSwitch('notcheck', '权限不检测', 0)
            ->setUrl(url('ruleSave'))
            ->setTrigger('pid', '', 'module', false)
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 规则通过复制添加
     * @throws
     */
    public function ruleCopy()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('菜单不存在', 'ruleIndex');
        }
        $rule_model = new RoleRuleModel();
        $rule       = $rule_model->get($id);
        if (!$rule) {
            $this->error('菜单不存在', 'ruleIndex');
        }
        //全部启用菜单
        $data   = $rule_model->getAll(3, '', 1);
        //全部模块
        $module_model = new ModuleModel();
        $modules = $module_model->getModule();
        $widget = new Widget();
        return $widget
            ->addSelect('pid', '父级权限', $data, $rule['pid'], '', '', ['default' => '顶级'])
            ->addSelect('module', '所属模块', $modules, $rule['module'], '', '', ['default' => '选择模块'])
            ->addText('title', '标题', $rule['title'], '*', 'required', 'text')
            ->addText('name', '模块/控制器/方法', $rule['name'], '* 不是实际方法时,使用default方法代替', 'required', 'text')
            ->addText('condition', '附加条件', $rule['condition'], '', '', 'text', ['placeholder'=>'{score}>5  and {score}<100'])
            ->addicon('icon', '图标', $rule['icon'], '只针对顶级栏目有效,例如：fa fa-tachometer')
            ->addText('sort', '排序', $rule['sort'], '* 从小到大排序', 'required', 'number')
            ->addSwitch('display', '是否显示', $rule['status'])
            ->addSwitch('notcheck', '权限不检测', $rule['notcheck'])
            ->setUrl(url('ruleSave'))
            ->setTrigger('pid', '', 'module', false)
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 规则添加操作
     */
    public function ruleSave()
    {
        $pid = input('pid', 0, 'intval');
        $rule_model = new RoleRuleModel();
        if ($pid == 0) {
            $module = input('module', 'admin');
        } else {
            $module = $rule_model->where('id', $pid)->value('module');
        }
        $rst        = $rule_model->add(input('name', ''), input('title', ''), $pid, input('notcheck', 0, 'intval'), input('sort', 10, 'intval'), input('icon', ''), $module, input('condition', ''));
        if ($rst && is_int($rst)) {
            Cache::clear();
            $no_frame = input('no_frame', 0, 'intval');
            if ($no_frame) {
                $this->success('权限添加成功', 'ruleIndex');
            } else {
                $this->success('权限添加成功', 'ruleIndex', ['is_frame' => 1]);
            }
        } else {
            $this->error('控制器或方法不存在,或提交格式不规范', 'ruleIndex');
        }
    }

    /**
     * 规则编辑
     * @throws
     */
    public function ruleEdit()
    {
        $id = input('id', 0);
        if (!$id) {
            $this->error('菜单不存在', 'ruleIndex');
        }
        $rule_model = new RoleRuleModel();
        $rule       = $rule_model->get($id);
        if (!$rule) {
            $this->error('菜单不存在', 'ruleIndex');
        }
        //全部启用菜单
        $data   = $rule_model->getAll(3, '', 1);
        //全部模块
        $module_model = new ModuleModel();
        $modules = $module_model->getModule();
        $widget = new Widget();
        return $widget
            ->addText('id', '', $id, '', '', 'hidden')
            ->addSelect('pid', '父级权限', $data, $rule['pid'], '', '', ['default' => '顶级'])
            ->addSelect('module', '所属模块', $modules, $rule['module'], '', '', ['default' => '选择模块'])
            ->addText('title', '标题', $rule['title'], '*', 'required', 'text')
            ->addText('name', '模块/控制器/方法', $rule['name'], '* 不是实际方法时,使用default方法代替', 'required', 'text')
            ->addText('condition', '附加条件', $rule['condition'], '', '', 'text', ['placeholder'=>'{score}>5  and {score}<100'])
            ->addicon('icon', '图标', $rule['icon'], '只针对顶级栏目有效,例如：fa fa-tachometer')
            ->addText('sort', '排序', $rule['sort'], '* 从小到大排序', 'required', 'number')
            ->addSwitch('status', '是否启用', $rule['status'])
            ->addSwitch('notcheck', '权限不检测', $rule['notcheck'])
            ->setUrl(url('ruleUpdate'))
            ->setTrigger('pid', '', 'module', false)
            ->setAjax('ajaxForm-noJump')
            ->fetch();
    }

    /**
     * 规则编辑操作
     */
    public function ruleUpdate()
    {
        $pid = input('pid', 0, 'intval');
        $rule_model = new RoleRuleModel();
        if ($pid == 0) {
            $module = input('module', 'admin');
        } else {
            $module = $rule_model->where('id', $pid)->value('module');
        }
        $rst = $rule_model->edit(input('id', 0, 'intval'), input('name'), input('title'), $pid, input('status', 1, 'intval'), input('notcheck', 0, 'intval'), input('sort', 10, 'intval'), input('icon', ''), $module, input('condition', ''));
        if (is_int($rst) && $rst) {
            Cache::clear();
            $this->success('菜单修改成功', 'ruleIndex', ['is_frame' => 1]);
        } elseif (is_string($rst)) {
            $this->error($rst, 'ruleIndex', ['is_frame' => 1]);
        } else {
            $this->error('菜单修改失败', 'ruleIndex', ['is_frame' => 1]);
        }
    }
    /**
     * 规则启用/禁用
     */
    public function ruleState()
    {
        $id         = input('id');
        $rule_model = new RoleRuleModel();
        $status     = $rule_model->where('id', $id)->value('status');
        $status     = $status ? 0 : 1;
        $rule_model->where('id', $id)->setField('status', $status);
        Cache::clear();
        $this->success($status ? '启用' : '禁用', null, ['result' => $status]);
    }

    /**
     * 规则检测/不检测
     */
    public function ruleNotcheck()
    {
        $id         = input('id');
        $rule_model = new RoleRuleModel();
        $status     = $rule_model->where('id', $id)->value('notcheck');
        $status     = $status ? 0 : 1;
        $rule_model->where('id', $id)->setField('notcheck', $status);
        Cache::clear();
        $this->success($status ? '不检测' : '检测', null, ['result' => !$status]);
    }

    /**
     * 规则排序
     * @throws
     */
    public function ruleOrder()
    {
        $datas = input('post.');
        $data  = [];
        foreach ($datas as $id => $sort) {
            $data[] = ['id' => $id, 'sort' => $sort];
        }
        $rule_model = new RoleRuleModel();
        $rst        = $rule_model->saveAll($data);
        if ($rst !== false) {
            Cache::clear();
            $this->success('排序更新成功', 'ruleIndex');
        } else {
            $this->error('排序更新失败', 'ruleIndex');
        }
    }

    /**
     * 规则删除
     */
    public function ruleDel()
    {
        $id  = input('id');
        $rule_model = new RoleRuleModel();
        $rst = $rule_model->del($id);
        if ($rst !== false) {
            Cache::clear();
            $this->success('菜单删除成功', 'ruleIndex');
        } else {
            $this->error('菜单删除失败', 'ruleIndex');
        }
    }
}
