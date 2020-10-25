<?php
namespace Admin\Controller;

use Admin\Model\MenuModel as Menu;
use Think\Controller;

class IndexController extends AdminController {

    public function index() {
        $Menu = new Menu();
        // 获取规范的菜单数组
        $sub_menu = $Menu->getMenuList();
        $this->topmenu = $Menu->getTopMenu(); // 顶部大分类菜单
        $this->assign('menu_json', json_encode($sub_menu));
        $this->display();
    }

    /**
     * 后台首页的系统信息
     */
    public function main() {
        $model = M();
        $mysql = $model->query("SELECT VERSION() as version");
        $this->mysql_version = $mysql[0]['version'];
        $this->display();
    }

}
