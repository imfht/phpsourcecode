<?php 
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */
namespace app\common\behavior;
use app\common\controller\Base;
use \think\Request;
use \think\Session;
use \think\Cookie;
use \think\Hook;
use \think\Lang;
class InitCache extends Base
{
    public function run(&$params)
    {
        if($this->request->module()=="install") return ;
        $this->initcache();
        $this->webclose();
    }

    private function initcache(){
    	//系统配置项
    	if(!cache('system_setting')){
    		cache('system_setting',model('Base')->getall('system_setting',['order'=>'id asc']));
    	}
    }
    private function webclose(){
    	//网站设置关闭时
    	$setting = cache('system_setting');
    	if(getset('site_close')=="Y"&&strtolower($this->request->module())=='index'){
            $this->error2(getset('close_notice'));
            
    		// die();
    	}
    }

}