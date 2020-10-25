<?php

namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 系统菜单管理.
 */
class SysMenu extends Base
{
    /**
     * 菜单模型
     * @var \App\Model\SysMenu
     */
    private $sysMenuModel;
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('系统管理', '/Admin/System/index');
        $this->sysMenuModel = model('SysMenu');
    }

    /**
     * 菜单管理界面.
     */
    public function index()
    {
        $this->setSeoTitle('菜单管理');
        $this->addBreadcrumb('菜单管理', $this->currentUrl);
        //菜单模块类别
        $sysMenu = new \App\Service\SysMenu();
        $this->assign('moduleTypeList', $sysMenu->getModuleTypeList());
        $moduleType = isset($_GET['moduleType']) && $_GET['moduleType'] ? $_GET['moduleType'] : $sysMenu::MENU_TYPE_ADMIN;
        $this->assign('moduleType', $moduleType);
        //菜单列表
        $menuList     = $this->sysMenuModel->getMenuList($moduleType);
        //树结构菜单列表
        $tree          = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->nameKey = 'menuName';
        $tree->load($menuList);
        //可嵌套列表
        $addHtml = '<button type="button" class="btn btn-outline btn-danger btn-xs pull-right del"><i class="fa fa-trash-o"></i>删除</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right edit" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>编辑</button>';
        $addHtml .= '<button type="button" class="btn btn-outline btn-primary btn-xs pull-right addchild" data-toggle="modal" data-target="#myModal"><i class="fa fa-pencil"></i>添加子菜单</button>';
        $nestableHtml = $tree->buildNestableTree($addHtml);
        $this->assign('nestableHtml', $nestableHtml);
        $this->display();
    }

    /**
     * 生成树结构option文本
     * @return string
     */
    public function getTreeOption()
    {
        $moduleType = isset($_GET['moduleType']) && $_GET['moduleType'] ? $_GET['moduleType'] : \App\Service\SysMenu::MENU_TYPE_ADMIN;
        $secId = isset($_GET['secId']) ? $_GET['secId'] : 0;
        //菜单列表
        $menuList     = $this->sysMenuModel->getMenuList($moduleType);
        //树结构菜单列表
        $tree          = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->nameKey = 'menuName';
        $tree->load($menuList);
        $secId && $tree->optionSelectId = $secId;
        //菜单选择列表
        $optionHtml = '<option value="0">顶级菜单</option>';
        $optionHtml .= $tree->buildOptions();
        $this->http->finish($optionHtml);
    }
    /**
     * 保存菜单数据.
     */
    public function save()
    {
        try {
            $postData              = $this->request->post;
            $postData['addUserId'] = $this->user->getUid();
            $sysMenu               = new \App\Service\SysMenu();
            $rs                    = $sysMenu->saveMenu($postData);
            if ($rs) {
                return $this->showMsg('success', ($postData['menuId'] ? '编辑' : '添加') . '成功', '/Admin/SysMenu/index');
            }
            throw new \Exception(($postData['menuId'] ? '编辑' : '添加') . '菜单失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 获取菜单数据.
     *
     * @return bool
     */
    public function get()
    {
        try {
            $id   = $this->request->get['menuId'] ?? 0;
            $postData = $this->sysMenuModel->getone(['menuId'=>$id]);

            return $this->showMsg('success', '获取成功', '', $postData);
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 保存排序层级数据.
     */
    public function saveSort()
    {
        try {
            $sortData = $this->request->post['sortData'];
            if (empty($sortData)) {
                throw new \Exception('排序数据有误');
            }
            $sysMenu = new \App\Service\SysMenu();
            $rs      = $sysMenu->saveSort($sortData);
            if ($rs) {
                return $this->showMsg('success', '保存排序成功');
            }
            throw new \Exception('保存排序失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }

    /**
     * 删除菜单
     * @return bool
     */
    public function del()
    {
        try {
            $id   = $this->request->post['menuId'] ?? 0;
            if (!$id){
                throw new \Exception('请指定要删除的菜单');
            }
            $rs = $this->sysMenuModel->set($id, ['isDel'=>1]);
            if ($rs){
                return $this->showMsg('success', '删除成功');
            }
            throw new \Exception('删除失败');
        } catch (\Exception $e) {
            return $this->showMsg('error', $e->getMessage());
        }
    }
}
