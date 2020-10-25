<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 初始化应用,并设置常用常量
 */
namespace app\common\behavior;
use think\facade\Env;

class AppInit{

    /**
     * 初始化行为入口
     */
    public function run(){
        $this->initPathConst();
    }

    /**
     * 初始化路径常量
     */
    private function initPathConst(){
        define('DS',DIRECTORY_SEPARATOR);
        define('PATH_TOOT',Env::get('root_path'));
        define('PATH_APP',Env::get('app_path'));
        define('PATH_PUBLIC', PATH_TOOT.'public'.DS);
        define('PATH_THEMES', PATH_TOOT.'themes'.DS);
        define('PATH_RES', PATH_PUBLIC.'res'.DS); 
        define('PATH_STATIC', PATH_PUBLIC.'static'.DS); 
    }
}