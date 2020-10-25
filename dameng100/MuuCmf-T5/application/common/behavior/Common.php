<?php
namespace app\common\behavior;

use think\Config;
use think\Db;
use think\Hook;
use think\Cache;
use think\Lang;

class Common
{

    public function moduleInit(&$request)
    {
        if(strtolower(request()->module())!='install'){
            //动态添加系统配置,非模块配置
            $config = Cache::get('DB_CONFIG_DATA');
            if (!$config) {
                $map['status'] = 1;
                $map['group']=['>',0];
                $data = Db::name('Config')->where($map)->field('type,name,value')->select();
                
                foreach ($data as $value) {
                    $config[$value['name']] = self::parse($value['type'], $value['value']);
                }
                Cache::set('DB_CONFIG_DATA', $config);
            }
            Config::set($config); //动态添加配置
        }
        // 判断站点是否关闭
        if (strtolower(request()->module()) != 'install' && strtolower(request()->module()) != 'admin') {
            if (!Config::get('WEB_SITE_CLOSE')) {
                header("Content-Type: text/html; charset=utf-8");
                echo Config::get('WEB_SITE_CLOSE_HINT');exit;
            }
        }

        // 非install模块加载钩子
        if(strtolower(request()->module())!='install'){  
            // 加载钩子
            $data = Cache::get('hooks');
            if(!$data){
                $hooks = collection(Db::name('Hooks')->column('name,addons'))->toArray();

                foreach ($hooks as $key => $value) {
                    
                    $addons_map['status']  =   1;
                    $names          =   explode(',',$value);
                    $addons_map['name']    =   ['in',$names];
                    $addons_map['is_setup'] = 1;
                    $data = Db::name('Addons')->where($addons_map)->column('id,name');
                    if($data){
                        $addons = array_filter(array_map('get_addon_class', $data));
                        Hook::add($key,$addons);
                    }
                }
                Cache::set('hooks',Hook::get());
            }else{
                Hook::import($data,false);
            }
        }

        // app_trace 调试模式后台设置
        if (Config::get('show_page_trace'))
        {
            Config::set('app_trace', true);
        }
        // app_debug 开发者调试模式
        if (Config::get('develop_mode'))
        {
            Config::set('app_debug', true);
        }
        // 如果是开发模式那么将异常模板修改成官方的
        if (Config::get('app_debug'))
        {
            Config::set('exception_tmpl', THINK_PATH . 'tpl' . DS . 'think_exception.tpl');
        }
        // 如果是trace模式且Ajax的情况下关闭trace
        if (Config::get('app_trace') && $request->isAjax())
        {
            Config::set('app_trace', false);
        }
        
        // 切换多语言
        if (Config::get('lang_switch_on') && $request->get('lang'))
        {
            \think\Cookie::set('think_var', $request->get('lang'));
        }
    }

    /**
     * 根据配置类型解析配置
     * @param  integer $type  配置类型
     * @param  string  $value 配置值
     */
    private static function parse($type, $value){
        switch ($type) {
            case 3: //解析数组
                $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
                if(strpos($value,':')){
                    $value  = array();
                    foreach ($array as $val) {
                        list($k, $v) = explode(':', $val);
                        $value[$k]   = $v;
                    }
                }else{
                    $value =    $array;
                }
                break;
        }
        return $value;
    }   

}
