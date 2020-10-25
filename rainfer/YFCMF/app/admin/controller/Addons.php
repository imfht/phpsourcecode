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

use app\common\controller\Base as BaseAddon;
use app\common\model\Addon as AddonModel;
use app\common\widget\Widget;

/**
 * 插件后台管理
 * @Author: rainfer <rainfer520@qq.com>
 */
class Addons extends BaseAddon
{
    /**
     * 初始化
     */
    protected function initialize(){
    	$init = new Base; 
    	$init -> initialize();    	
    }

    /**
     * 插件列表
     */
    public function addonsIndex()
    {
        $addons_model = new AddonModel;
        //本地
        $addons = $addons_model->getAll();
        //插件市场
        $addons_online = $addons_model->getAddonsOnlines();
        //表格字段
        $fields = [
            ['title' => 'ID', 'field' => 'id'],
            ['title' => '排序', 'field' => 'sort', 'type' => 'input'],
            ['title' => '插件标识', 'field' => 'name', 'type' => 'html'],
            ['title' => '插件名称', 'field' => 'title'],
            ['title' => '插件描述', 'field' => 'intro'],
            ['title' => '分类', 'field' => 'category'],
            ['title' => '作者', 'field' => 'author', 'type' => 'html'],
            ['title' => '版本', 'field' => 'version'],
            ['title' => '操作', 'field' => 'actions', 'type' => 'html']
        ];
        //主键
        $pk = 'id';
        //右侧操作按钮
        $right_action = [
        ];
        $order        = url('admin/Addons/addonsOrder');
        //在线
        $fields_online = [
            ['title' => '插件标识', 'field' => 'name'],
            ['title' => '插件名称', 'field' => 'title', 'type' => 'html'],
            ['title' => '插件描述', 'field' => 'intro'],
            ['title' => '分类', 'field' => 'category'],
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
                        'title' => '本地插件',
                        'href'  => '',
                        'items' => [
                            ['table', $fields, $pk, $addons, $right_action, '', $order]
                        ]
                    ],
                    [
                        'title' => '插件市场',
                        'href'  => '',
                        'items' => [
                            ['table', $fields_online, $pk, $addons_online['lists'], $right_action]
                        ]
                    ]
                ]
            )
            ->setButton()
            ->fetch();
    }

    /**
     * 安装插件
     * @throws
     */
    public function install()
    {
        $addon_name = input('name', '');
        $addon_model = new AddonModel();
        if ($addon_model->install($addon_name)) {
            cache('addon_all', null);
            cache('hook_addons', null);
            $this->success('插件安装成功');
        } else {
            $this->error('插件安装失败');
        }
    }

    /**
     * 卸载插件
     * @throws
     */
    public function uninstall()
    {
        $addon_name = input('name', '');
        $addon_model = new AddonModel();
        if ($addon_model->uninstall($addon_name)) {
            cache('addon_all', null);
            cache('hook_addons', null);
            $this->success('插件卸载成功');
        } else {
            $this->error('插件卸载失败');
        }
    }

    /**
     * 禁用插件
     */
    public function disable()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('插件不存在', 'addonsIndex');
        }
        $addon_model = new AddonModel();
        if ($addon_model->disable($id)) {
            cache('addon_all', null);
            cache('hook_addons', null);
            $this->success('禁用成功', 'addonsIndex');
        } else {
            $this->error('禁用失败', 'addonsIndex');
        }
    }

    /**
     * 启用插件
     */
    public function enable()
    {
        $id = input('id');
        if (empty($id)) {
            $this->error('插件不存在', 'addonsIndex');
        }
        $addon_model = new AddonModel();
        if ($addon_model->enable($id)) {
            cache('addon_all', null);
            cache('hook_addons', null);
            $this->success('启用成功', 'addonsIndex');
        } else {
            $this->error('启用失败', 'addonsIndex');
        }
    }

    /**
     * 插件排序
     * @throws
     */
    public function addonsOrder()
    {
        $list = [];
        foreach (input('post.') as $id => $sort) {
            $list[] = ['id' => $id, 'sort' => $sort];
        }
        $model = new AddonModel();
        $model->saveAll($list);
        cache('addon_all', null);
        cache('hook_addons', null);
        $this->success('排序成功', 'addonsIndex');
    }
    /**
     * 远程安装插件
     * @throws
     */
    public function installOnline()
    {
        $name = input('name');
        if (!$name) {
            $this->error('安装出错', 'addonsIndex');
        }
        $addon_model = new AddonModel();
        $params = [
            'uid'=>config('yfcmf.api_addon.uid'),
            'token'=>config('yfcmf.api_addon.token'),
            'version'=>input('version'),
            'yf_version'=>config('yfcmf.yfcmf_version')
        ];
        if ($addon_model->installOnline($name, $params)) {
            $this->success('安装成功', 'addonsIndex');
        } else {
            $this->error('安装失败', 'addonsIndex');
        }
    }
    /**
     * 升级插件
     * @throws
     */
    public function upgrade()
    {
        $name = input('name');
        if (!$name) {
            $this->error('升级出错', 'addonsIndex');
        }
        $addon_model = new AddonModel();
        $params = [
            'uid'=>config('yfcmf.api_addon.uid'),
            'token'=>config('yfcmf.api_addon.token'),
            'version'=>input('version'),
            'yf_version'=>config('yfcmf.yfcmf_version')
        ];
        if ($addon_model->upgrade($name, $params)) {
            $this->success('升级成功', 'addonsIndex');
        } else {
            $this->error('升级失败', 'addonsIndex');
        }
    }
}
