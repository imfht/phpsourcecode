<?php
namespace app\common\behavior;

use think\Loader;
use think\Route;

// 初始化钩子信息
class InitHook {

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        // 注册类的根命名空间
            Loader::addNamespace('addons', ADDONS_PATH);
            // 定义路由
            Route::any('addons/execute/:route', "\\muucmf\\addons\\Route@execute");
    }
}