<?php

namespace app\script\controller;

use app\script\model\Menu as MenuModel;
use app\script\service\MenuService;

/**
 * 菜单脚本
 * @author 牧羊人
 * @date 2019/6/25
 * Class Menu
 * @package app\script\controller
 */
class Menu extends BaseScript
{
    /**
     * 初始化方法
     * @author zongjl
     * @date 2019/6/25
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new MenuModel();
        $this->service = new MenuService();
    }

    /**
     * 脚本入口
     * @author zongjl
     * @date 2019/6/25
     */
    public function index()
    {
        $this->service->updateMenu();
    }

    public function menu()
    {
        $this->service->menu();
    }

    public function test()
    {
        $this->service->test();
    }
}
