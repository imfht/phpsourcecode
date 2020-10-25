<?php
namespace addons\cron;  // 注意命名空间规范


use think\addons\Addons;
use addons\cron\model\Crons as DM;

/**
 * WSTMart 计划任务功能
 * @author WSTMart
 */
class Cron extends Addons{
    // 该插件的基础信息
    public $info = [
        'name' => 'Cron',   // 插件标识
        'title' => '计划任务',  // 插件名称
        'description' => '计划任务管理，若用户没有在系统里配置定时任务则建议开启该插件',    // 插件简介
        'status' => 0,  // 状态
        'author' => 'WSTMart',
        'version' => '1.0.0'
    ];

	
    /**
     * 插件安装方法
     * @return bool
     */
    public function install(){
        $m = new DM();
        $flag = $m->install();
        WSTClearHookCache();
        return $flag;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall(){
        $m = new DM();
        $flag = $m->uninstall();
        WSTClearHookCache();
        return $flag;
    }
    
	/**
     * 插件启用方法
     * @return bool
     */
    public function enable(){
    	WSTClearHookCache();
        return true;
    }
    
    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable(){
    	WSTClearHookCache();
    	return true;
    }
    
    /**
     * 插件设置方法
     * @return bool
     */
    public function saveConfig(){
    	WSTClearHookCache();
    	return true;
    }

    public function initCronHook($params){
        echo "<img style='display:none' src='".request()->root(true)."/addon/cron-cron-runCrons.html'>";
    }
}