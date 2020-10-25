<?php

namespace App\Service;

/**
 * 系统菜单服务类.
 */
class SysMenu
{
    const MENU_TYPE_PUBLIC = 'public';
    const MENU_TYPE_ADMIN  = 'admin';
    const MENU_TYPE_INDEX  = 'index';
    const MENU_TYPE_USER   = 'user';
    /**
     * 模块列表.
     *
     * @var
     */
    private $moduleTypeList;
    /**
     * 菜单列表.
     *
     * @var
     */
    private $menuList;
    //图标后缀标签
    private $adminMenuLableList = [];

    /**
     * @var \App\Model\SysMenu
     */
    private $sysMenuModel;
    /**
     * 当前选中菜单ID集.
     *
     * @var
     */
    private $sysAdminCurrentIds = [];

    /**
     * 构造函数.
     */
    public function __construct()
    {
        $this->sysMenuModel = model('SysMenu');
    }

    /**
     * 获取模块列表
     * @return array
     */
    public function getModuleTypeList()
    {
        $this->moduleTypeList = [
            self::MENU_TYPE_ADMIN  => '后台模块',
            self::MENU_TYPE_INDEX  => '前台模块',
            self::MENU_TYPE_PUBLIC => '公共模块',
            self::MENU_TYPE_USER   => '用户模块',
        ];
        return $this->moduleTypeList;
    }

    /**
     * 获取菜单属性数组结构.
     *
     * @param $moduleType
     *
     * @return array
     */
    public function getTreeMenuList($moduleType)
    {
        $this->menuList = $this->sysMenuModel->getMenuList($moduleType);

        if (\Swoole::$php->user->isLogin()){
            $userId = \Swoole::$php->user->getUid();
        }else{
            $userId = 0;
        }
        foreach ($this->menuList as $k => $v) {
            //无权限则删除
            $isValid = \Swoole::$php->rbac->check(strtolower($v['url']), $userId);
            if (!$isValid) {
                unset($this->menuList[$k]);
            }
        }

        $tree = new \App\Common\Tree('menuId', 'parentId', 'child');
        $tree->load($this->menuList);
        $treelist = $tree->deepTree();

        return $treelist;
    }

    /**
     * 生成后台左侧菜单.
     *
     * @param mixed $currentUrl
     *
     * @return string
     */
    public function buildAdminTreeMenu($currentUrl = '')
    {
        $treeList = $this->getTreeMenuList('admin');
        //初始化标签列表
        $this->initAdminLableList();
        //初始化当前菜单ID集
        $currentMenuData = $this->sysMenuModel->getMenuDataByUrl($currentUrl);
        if ($currentMenuData) {
            $this->initCurrentIds($currentMenuData['menuId']);
        }

        return $this->compineAdminTreeMenu($treeList);
    }

    /**
     * 获取当前菜单ID集.
     *
     * @param $parentId
     */
    private function initCurrentIds($parentId)
    {
        if ($parentId > 0 && isset($this->menuList[$parentId])) {
            $this->sysAdminCurrentIds[] = $parentId;
            if ($this->menuList[$parentId]['menuId'] > 0) {
                $this->initCurrentIds($this->menuList[$parentId]['parentId']);
            }
        }
    }

    /**
     * 初始化菜单标签列表.
     */
    private function initAdminLableList()
    {
    }

    /**
     * 合并后台菜单html.
     *
     * @param mixed $menuList
     * @param mixed $menuLevel
     */
    private function compineAdminTreeMenu($menuList = [], $menuLevel = 1)
    {
        $html = '';
        if ($menuList) {
            if ($menuLevel > 1) {
                $levelList = [2 => 'nav-second-level', 3 =>'nav-third-level'];
                $html .= '<ul class="nav '.(isset($levelList[$menuLevel]) ? $levelList[$menuLevel] : '').' collapse">';
            }
            foreach ($menuList as $menuOne) {
                $menuUrl = strtolower($menuOne['url']);
                //当前菜单选中状态
                $html .= '<li ' . (in_array($menuOne['menuId'], $this->sysAdminCurrentIds) ? 'class="active"' : '') . '>';
                //链接
                $html .= '<a href="' . (isset($menuOne['child']) && $menuOne['child'] ? '#' : $menuOne['url']) . '">';
                //菜单前面图标
                if (isset($menuOne['iconClass']) && $menuOne['iconClass']) {
                    $html .= '<i class="' . $menuOne['iconClass'] . '"></i>';
                }
                $html .= '<span class="nav-label">' . $menuOne['menuName'] . ' </span>';
                //标签
                if (isset($this->adminMenuLableList[$menuUrl])) {
                    $html .= $this->adminMenuLableList[$menuUrl];
                } elseif (isset($menuOne['child']) && $menuOne['child']) {
                    $html .= '<span class="fa arrow"></span>';
                }
                $html .= '</a>';
                if (isset($menuOne['child']) && $menuOne['child']) {
                    $html .= $this->compineAdminTreeMenu($menuOne['child'], $menuLevel + 1);
                }
            }
            if ($menuLevel > 1) {
                $html .= '</ul>';
            }
        }

        return $html;
    }
    /**
     * 保存菜单排序数据
     * @param $sortData
     * @return bool
     */
    public function saveSort($sortData)
    {
        $this->sysMenuModel->start();
        try{
            $this->saveSortData($sortData);
            $this->sysMenuModel->commit();
            return true;
        }catch (\Exception $e){
            $this->sysMenuModel->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 保存排序数据
     * @param $list
     * @param int $parentId
     * @return bool
     */
    private function saveSortData($list, $parentId = 0)
    {
        if ($list) {
            foreach ($list as $k => $v) {
                $id = isset($v['id']) && $v['id'] ? (int)$v['id'] : 0;
                $id && $this->sysMenuModel->set($id, ['orderNum' => $k, 'parentId' => $parentId]);
                if (isset($v['children']) && $v['children']) {
                    $this->saveSortData($v['children'], $id);
                }
            }
            return true;
        }
    }
    /**
     * 保存菜单数据
     * @param $menuData
     */
    public function saveMenu($menuData)
    {
        $id = (int) $menuData['menuId'];
        $saveData = [
            'moduleType' => $menuData['moduleType'],
            'menuName' => $menuData['menuName'],
            'parentId' => $menuData['parentId'],
            'url' => $menuData['url'],
            'iconClass' => $menuData['iconClass'],
        ];
        //判断是否重名
        $existsWhere = ['parentId'=>$saveData['parentId'], 'menuName'=>$saveData['menuName'],'isDel'=>0];
        $id && $existsWhere['menuId !'] = $id;
        $findExists = $this->sysMenuModel->exists($existsWhere);
        if ($findExists){
            throw new \Exception('该层级已存在同名菜单');
        }
        if ($id){//修改
            return $this->sysMenuModel->set($id, $saveData);
        }else{//添加
            //排序最大值
            $maxOrderNum = $this->sysMenuModel->getMax('orderNum', ['moduleType'=>$menuData['moduleType']]);
            $saveData['orderNum'] = $maxOrderNum + 1;
            $saveData['addUserId'] = $menuData['addUserId'];
            $saveData['addTime'] = time();
            return $this->sysMenuModel->put($saveData);
        }
    }
}
