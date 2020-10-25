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
namespace app\admin\controller;

use app\common\widget\Widget;
use app\common\model\Module as ModuleModel;
use think\Db;
use think\facade\Cache;

/**
 * 模块管理
 * @Author: rainfer <rainfer520@qq.com>
 */
class Modules extends Base
{

    /**
     * 模块列表
     */
    public function modulesIndex()
    {
        $modules_model = new ModuleModel();
        //本地
        $modules = $modules_model->getAll();
        //插件市场
        $modules_online = $modules_model->getModulesOnlines();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '模块标识', 'field' => 'name', 'type' => 'html'],
            ['title' => '模块名称', 'field' => 'title'],
            ['title' => '模块描述', 'field' => 'intro'],
            ['title' => '作者', 'field' => 'author', 'type' => 'html'],
            ['title' => '版本', 'field' => 'version'],
            ['title' => '操作', 'field' => 'actions', 'type' => 'html']
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
        ];
        //在线
        $fields_online = [
            ['title' => '模块标识', 'field' => 'name'],
            ['title' => '模块名称', 'field' => 'title', 'type' => 'html'],
            ['title' => '模块描述', 'field' => 'intro'],
            ['title' => '作者', 'field' => 'author', 'type' => 'html'],
            ['title' => '价格', 'field' => 'price'],
            ['title' => '下载', 'field' => 'downloads'],
            ['title' => '版本', 'field' => 'version'],
            ['title' => '操作', 'field' => 'actions', 'type' => 'html']
        ];
        $widget       = new Widget();
        return $widget
            ->addGroup(
                [
                    [
                        'title' => '本地模块',
                        'href'  => '',
                        'items' => [
                            ['table', $fields, $pk, $modules, $right_action, '']
                        ]
                    ],
                    [
                        'title' => '模块市场',
                        'href'  => '',
                        'items' => [
                            ['table', $fields_online, $pk, $modules_online, $right_action]
                        ]
                    ]
                ]
            )
            ->setButton()
            ->fetch();
    }

    /**
     * 安装模块
     *
     * @return mixed
     * @throws \Exception
     */
    public function install()
    {
        $name = input('name', '');
        if (!$name) {
            $this->error('安装出错');
        }
        $module_model = new ModuleModel();
        $sys_modules = $module_model->getSysModules();
        if (isset($sys_modules[$name])) {
            $this->error('禁止操作系统核心模块！');
        }
        // 模块配置信息
        $module_info = $module_model->getInfoFromFile($name);
        if (request()->isAjax()&&request()->isPost()) {
            if ($module_model->install($name)) {
                $this->success('安装成功！', 'modulesIndex', ['is_frame' => 1]);
            } else {
                $this->error('安装出错');
            }
        } else {
            $need_module = [];
            $need_addon = [];
            $table_check = [];
            // 检查模块依赖
            if (isset($module_info['need_module']) && !empty($module_info['need_module'])) {
                $need_module = $module_model->checkNeeds('module', $module_info['need_module']);
            }
            // 检查插件依赖
            if (isset($module_info['need_addon']) && !empty($module_info['need_addon'])) {
                $need_addon = $module_model->checkNeeds('addon', $module_info['need_addon']);
            }
            // 检查数据表
            if (isset($module_info['tables']) && !empty($module_info['tables'])) {
                foreach ($module_info['tables'] as $table) {
                    if (Db::query("SHOW TABLES LIKE '".config('database.prefix')."{$table}'")) {
                        $table_check[] = [
                            'table' => config('database.prefix')."{$table}",
                            'result' =>  '<span class="text-danger">存在同名</span>'
                        ];
                    } else {
                        $table_check[] = [
                            'table' => config('database.prefix')."{$table}",
                            'result' => '<i class="fa fa-check text-success"></i>'
                        ];
                    }
                }
            }
            $this->assign('need_module', $need_module);
            $this->assign('need_addon', $need_addon);
            $this->assign('table_check', $table_check);
            $this->assign('name', $name);
            return $this->fetch();
        }
    }

    /**
     * 卸载模块
     *
     * @return mixed
     * @throws \Exception
     */
    public function uninstall()
    {
        $name = input('name', '');
        $clear = input('clear', 0, 'intval');
        if (!$name) {
            $this->error('模块不存在！');
        }
        $module_model = new ModuleModel();
        $sys_modules = $module_model->getSysModules();
        if (isset($sys_modules[$name])) {
            $this->error('禁止操作系统核心模块！');
        }
        if ($module_model->uninstall($name, $clear)) {
            $this->success('卸载成功', 'modulesIndex');
        } else {
            $this->error('卸载失败');
        }
    }

    /**
     * 禁用模块
     */
    public function disable()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('模块不存在', 'modulesIndex');
        }
        $module_model = new ModuleModel();
        if ($module_model->disable($id)) {
            Cache::clear();
            $this->success('禁用成功', 'modulesIndex');
        } else {
            $this->error('禁用失败', 'modulesIndex');
        }
    }

    /**
     * 启用模块
     */
    public function enable()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('模块不存在', 'modulesIndex');
        }
        $module_model = new ModuleModel();
        if ($module_model->enable($id)) {
            Cache::clear();
            $this->success('启用成功', 'modulesIndex');
        } else {
            $this->error('启用失败', 'modulesIndex');
        }
    }
    /**
     * 远程安装模块
     * @throws
     */
    public function installOnline()
    {
        $name = input('name');
        if (!$name) {
            $this->error('安装出错', 'modulesIndex');
        }
        $module_model = new ModuleModel();
        $params = [
            'uid'=>config('yfcmf.api_module.uid'),
            'token'=>config('yfcmf.api_module.token'),
            'version'=>input('version'),
            'yf_version'=>config('yfcmf.yfcmf_version')
        ];
        if ($module_model->installOnline($name, $params)) {
            $this->success('安装成功', 'modulesIndex');
        } else {
            $this->error('安装失败', 'modulesIndex');
        }
    }
    /**
     * 升级模块
     * @throws
     */
    public function upgrade()
    {
        $name = input('name');
        if (!$name) {
            $this->error('升级出错', 'modulesIndex');
        }
        $module_model = new ModuleModel();
        $params = [
            'uid'=>config('yfcmf.api_addon.uid'),
            'token'=>config('yfcmf.api_addon.token'),
            'version'=>input('version'),
            'yf_version'=>config('yfcmf.yfcmf_version')
        ];
        if ($module_model->upgrade($name, $params)) {
            $this->success('升级成功', 'modulesIndex');
        } else {
            $this->error('升级失败', 'modulesIndex');
        }
    }
}
