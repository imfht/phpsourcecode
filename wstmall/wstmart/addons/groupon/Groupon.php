<?php
namespace addons\groupon;  // 注意命名空间规范


use think\addons\Addons;
use addons\groupon\model\Groupons as DM;

/**
 * WSTMart 团购插件
 * @author WSTMart
 */
class Groupon extends Addons{
    // 该插件的基础信息
    public $info = [
        'name' => 'Groupon',   // 插件标识
        'title' => '团购活动',  // 插件名称
        'description' => 'WSTMart团购活动插件',    // 插件简介
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
        $flag = $m->installMenu();
    	WSTClearHookCache();
        cache('hooks',null);
        return $flag;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall(){
        $m = new DM();
        $flag = $m->uninstallMenu();
        WSTClearHookCache();
        cache('hooks',null);
        return $flag;
    }
    
	/**
     * 插件启用方法
     * @return bool
     */
    public function enable(){
        $m = new DM();
        $flag = $m->toggleShow(1);
        WSTClearHookCache();
        cache('hooks',null);
        return $flag;
    }
    
    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable(){
        $m = new DM();
        $flag = $m->toggleShow(0);
        WSTClearHookCache();
        cache('hooks',null);
        return $flag;
    }

    /**
     * 插件设置方法
     * @return bool
     */
    public function saveConfig(){
        WSTClearHookCache();
        cache('hooks',null);
        return true;
    }
    /**
     * 商品编辑之后执行
     */
    public function afterEditGoods($params){
        $m = new DM();
        $m->changeGroupon($params);
        return true;
    }
    /**
     * 订单取消之后执行
     */
    public function afterCancelOrder($params){
        $m = new DM();
        $m->cancelOrder($params);
        return true;
    }
    
}