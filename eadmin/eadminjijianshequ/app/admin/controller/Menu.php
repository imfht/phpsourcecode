<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 菜单控制器
 */
class Menu extends AdminBase
{

    /**
     * 菜单列表
     */
    public function menuList()
    {

        $where = empty($this->param['pid']) ? ['pid' => 0] : ['pid' => $this->param['pid']];

        $where['status|>='] = 0;

        $clist = $this->menuLogic->getMenuList($where, true, '', DB_LIST_ROWS);

        $this->assign('list', $clist['data']);

        $this->assign('page', $clist['page']);

        $this->assign('pid', $where['pid']);

        $parentpid = $this->menuLogic->getMenuInfo(['id' => $where['pid']]);
        if (!empty($parentpid)) {
            $this->assign('parentpid', $parentpid['pid']);
        }


        return $this->fetch('menu_list');
    }

    /**
     * 获取菜单Select结构数据
     */
    public function getMenuSelectData()
    {

        $menu_select = $this->menuLogic->menuToSelect($this->authMenuTree);

        $this->assign('menu_select', $menu_select);
    }

    /**
     * 菜单添加
     */
    public function menuAdd()
    {

        $this->param['module'] = MODULE_NAME;

        IS_POST && $this->jump($this->menuLogic->menuAdd($this->param));

        //获取菜单Select结构数据
        $this->getMenuSelectData();

        !empty($this->param['pid']) && $this->assign('info', ['pid' => $this->param['pid']]);

        return $this->fetch('menu_add');
    }

    /**
     * 菜单编辑
     */
    public function menuEdit()
    {

        IS_POST && $this->jump($this->menuLogic->menuEdit($this->param));

        $info = $this->menuLogic->getMenuInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);

        //获取菜单Select结构数据
        $this->getMenuSelectData();

        return $this->fetch('menu_edit');
    }

    /**
     * 菜单批量删除
     */
    public function menuAlldel($ids = 0)
    {

        $this->jump($this->menuLogic->menuDel(['id' => $ids]));
    }

    /**
     * 菜单删除
     */
    public function menuDel($id = 0)
    {

        $this->jump($this->menuLogic->menuDel(['id' => $id]));
    }
}
