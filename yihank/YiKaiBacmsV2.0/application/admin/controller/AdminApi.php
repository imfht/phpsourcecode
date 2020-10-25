<?php
namespace app\admin\controller;

class AdminApi extends Admin{
    /**
     * 获取菜单
     * @return mixed
     */
    public function index(){
        $menuList = model('admin/AdminMenu')->getMenu($this->loginUserInfo);
        return json($menuList);
    }
}
