<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\logic\Addon as LogicAddon;
use app\common\controller\HomeBase;
/**
 * 插件控制器
 */
class Addon extends HomeBase
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
        
        $class_path = SYS_DS_CONS . SYS_ADDON_DIR_NAME . SYS_DS_CONS . $addon_name . SYS_DS_CONS . LAYER_CONTROLLER_NAME . SYS_DS_CONS . $controller_name;
        
        $controller = new $class_path();
        
        $controller->$action_name();
    }
    
   
}
