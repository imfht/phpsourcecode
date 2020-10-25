<?php
namespace app\ebcms\behavior;

class Clearcache
{

    public function run(&$params)
    {
        // 当前系统已经安装的app缓存
        \think\Cache::set('eb_apps','');
        // 更新tag缓存
        \think\Cache::set('eb_tags','');
        // 更新路由缓存
        \think\Cache::set('eb_routes','');
        // 更新配置缓存
        \ebcms\Config::config(true);
    }
}