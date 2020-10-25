<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{

    /**
     * 加载公共函数库
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initFunction(Yaf_Dispatcher $dispatcher)
    {
        Yaf_Loader::import('function/helper.php');
        import('common');
    }

    /**
     * 加载数据库
     */

    public function _initDatabase(Yaf_Dispatcher $dispatcher)
    {
        \think\Db::setConfig();
    }

    /**
     * 设置缓存配置
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initCache(Yaf_Dispatcher $dispatcher)
    {
        $cache = \think\Cache::init();
        Yaf_Registry::set('cache', $cache);
    }
}