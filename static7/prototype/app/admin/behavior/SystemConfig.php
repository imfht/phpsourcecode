<?php

namespace app\admin\behavior;

use think\Config;
use think\Cache;
use think\Loader;

/**
 * Description of SystemConfig
 * 系统配置初始化
 * @author static7
 */
class SystemConfig {
    /**
     * 系统配置读取并缓存
     * @author staitc7 <static7@qq.com>
     */

    public function run() {
        $config = Cache::get('db_config_data'); 
        if (empty($config)) {
            $config = Loader::model('Deploy', 'api')->lists();
            Cache::set('db_config_data', $config);
            \think\Log::record("[ 系统配置 ]：初始化成功");
        }
        Config::set($config);
    }

}
