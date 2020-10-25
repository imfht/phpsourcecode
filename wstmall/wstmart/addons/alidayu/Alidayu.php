<?php
namespace addons\alidayu;  // 注意命名空间规范


use think\addons\Addons;
use addons\alidayu\model\Alidayu as DM;

/**
 * WSTMart 阿里大于短信接口
 * @author WSTMart
 */
class Alidayu extends Addons{
    // 该插件的基础信息
    public $info = [
        'name' => 'Alidayu',   // 插件标识
        'title' => '短信接口(阿里大于)',  // 插件名称
        'description' => '<font color="red">注意：阿里大于仅支持老客户，新用户请使用"阿里云-云通信"插件</font>',    // 插件简介
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
    	cache('hooks',null);
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
    	cache('hooks',null);
        return $flag;
    }
    
	/**
     * 插件启用方法
     * @return bool
     */
    public function enable(){
    	WSTClearHookCache();
    	cache('hooks',null);
        return true;
    }
    
    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable(){
    	WSTClearHookCache();
    	cache('hooks',null);
    	return true;
    }

    /**
     * 插件设置方法
     * @return bool
     */
    public function saveConfig(){
    	WSTClearHookCache();
        cache('alidayu_sms',null);
    	cache('hooks',null);
    	return true;
    }
    
    /**
     * 阿里大于短信服务商
     * @param string $phoneNumer  手机号码
     * @param string $content     短信内容
     */
    function sendSMS($params){
       $dm = new DM();
       $dm->sendSMS($params);
       return true;
    }
    
}