<?php

namespace app\common\behavior;

use app\common\model\Hook as HookModel;
use app\common\model\Hook_plugin as HookPluginModel;

/**
 * 注册钩子
 * @package app\common\behavior
 */
class Hook
{
    /**
     * 执行行为 run方法是Behavior唯一的接口
     * @access public
     * @param mixed $params  行为参数
     * @return void
     */
    public function run(&$params)
    {
        if(defined('BIND_MODULE') && BIND_MODULE === 'install') return;

        $hook_plugins = cache('hook_plugins');
        $hooks = cache('hooks');

        if (!$hook_plugins) {
            // 所有接口
            $hooks = HookModel::where('ifopen', 1)->column('id,name');
            //所有接口功能
            $hook_plugins = HookPluginModel::where('ifopen', 1)->order('list desc,id asc')->column(true);
            cache('hook_plugins', $hook_plugins);
            cache('hooks', $hooks);
        }
        $array = [];
        //接口关闭的话,该接口下的所有钩子都禁用
        foreach ($hook_plugins as $rs) {
            if ( in_array($rs['hook_key'], $hooks) ) {
                $key = $rs['hook_key'].'-'.$rs['hook_class'];
                if ($array[$key]===true) {
                    continue ;  //避免重复安装的钩子，重复执行
                }
                $array[$key] = true;
                if($rs['plugin_key'] && empty(plugins_config($rs['plugin_key']))){
                    //continue ; //如果插件卸载掉的话,就不要执行了
                }
                if(!class_exists($rs['hook_class'])){
                    continue ;
                }
                \think\Hook::add($rs['hook_key'], $rs['hook_class']);
            }
        }
    }
    
}
