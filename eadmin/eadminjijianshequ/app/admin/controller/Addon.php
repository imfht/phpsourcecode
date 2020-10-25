<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\logic\Addon as LogicAddon;

/**
 * 插件控制器
 */
class Addon extends AdminBase
{

    // 插件逻辑
    private static $addonLogic = null;

    /**
     * 构造方法
     */
    public function _initialize()
    {

        parent::_initialize();

        self::$addonLogic = get_sington_object('addonLogic', LogicAddon::class);
    }

    /**
     * 执行插件控制器
     */
    public function execute($addon_name = null, $controller_name = null, $action_name = null)
    {

        $class_path = "\\" . SYS_ADDON_DIR_NAME . "\\" . $addon_name . "\\" . LAYER_CONTROLLER_NAME . "\\" . $controller_name;

        $controller = new $class_path();

        $controller->$action_name();
    }

    /**
     * 执行插件安装
     */
    public function addonInstall($name = null)
    {

        $strtolower_name = strtolower($name);

        $class_path = "\\" . SYS_ADDON_DIR_NAME . "\\" . $strtolower_name . "\\" . $name;

        self::$addonLogic->executeSql($strtolower_name, 'install');

        $controller = new $class_path();

        $this->jump($controller->addonInstall());
    }

    /**
     * 执行插件卸载
     */
    public function addonUninstall($name = null)
    {

        $strtolower_name = strtolower($name);

        $class_path = "\\" . SYS_ADDON_DIR_NAME . "\\" . $strtolower_name . "\\" . $name;

        self::$addonLogic->executeSql($strtolower_name, 'uninstall');

        $controller = new $class_path();

        $this->jump($controller->addonUninstall());
    }

    /**
     * 插件配置
     */
    public function addonConfig($name = '')
    {


        IS_POST && $this->jump(self::$addonLogic->setAddonConfig($this->param));
        $config = self::$addonLogic->getAddonConfig($name);
        $this->assign('list', $config);
        $this->assign('custom_config', $config['custom_config']);

        return $this->fetch('addon_config');
    }

    /**
     * 插件列表
     */
    public function addonList()
    {

        $this->assign('list', self::$addonLogic->getAddonList());

        return $this->fetch('addon_list');
    }

    /**
     * 钩子列表
     */
    public function hookList()
    {
        $clist = self::$addonLogic->getHookList();
        $this->assign('list', $clist['data']);
        $this->assign('page', $clist['page']);
        return $this->fetch('hook_list');
    }

    /**
     * 插件后台显示页面
     *
     * @param string $name 插件名
     */
    public function adminlist($name, $map = [])
    {

        $data = self::$addonLogic->getAdminList($name, $map);


        $this->assign('list', $data['list']['data']);
        $this->assign('page', $data['list']['page']);

        $this->assign('listKey', $data['param']);
        if (!empty($data['custom_adminlist'])) {
            return $this->fetch($data['custom_adminlist']);
        } else {
            return $this->fetch('addon_adminlist');
        }


    }
}
