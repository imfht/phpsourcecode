<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:20
 */

namespace naples\lib;

use naples\lib\base\Service;

class Core extends Service
{
    /** 主流程 */
    public function init(){
        tick('加载配置');Factory::getConfig();tick('加载配置');
        tick('加载助手函数');Factory::getHelp();tick('加载助手函数');
        tick('加载错误管理');Factory::getErrorCatch();tick('加载错误管理');
        tick('注册关闭处理函数');$this->onShutdown();tick('注册关闭处理函数');
        tick('服务器设置');$this->options();tick('服务器设置');
        tick('预处理');$this->onLoad();tick('预处理');
        tick('控制器调度');Factory::getDispatch()->start();tick('控制器调度');

    }

    /**发送一些设置*/
    private function options(){
        clearstatcache();
        if (config('auto_session_start') and !isset($_SESSION)){session_start();}
        if (config('charset')){
            header("Content-type:text/html;charset=".config('charset'));//定义编码
        }
        if (config('enable_ini_set')){
            date_default_timezone_set(config('timezone'));//定义时区
            set_time_limit(config('time_limit'));//设置超时时间
            ini_set('always_populate_raw_post_data', -1);//we don't use $HTTP_RAW_POST_DATA
            ini_set('memory_limit', config('memory_limit'). 'M');//设置运行最大内存
            ini_set('max_input_time', config('max_input_time'));//设置表单提交最大时间
            ini_set('post_max_size', config('post_max_size'). 'M');//设置post最大数据
            ini_set('upload_max_filesize',config('upload_max_filesize'). 'M');//设置文件上传的最大文件上限
            ini_set('ignore_repeated_errors', config('ignore_repeated_errors'));//忽略重复的错误
            ini_set('ignore_repeated_source', config('ignore_repeated_source'));//忽略重复的错误来源
            ini_set('xdebug.var_display_max_children', config('xdebug_var_display_max_children')); // 最多孩子节点数
            ini_set('xdebug.var_display_max_data', config('xdebug_var_display_max_data'));// 最大字节数
            ini_set('xdebug.var_display_max_depth', config('xdebug_var_display_max_depth'));// 最大深度
        }
    }

    /** 注册结束处理函数 */
    private function onShutdown(){
        //记录访问记录到日志
        register_shutdown_function(function (){
            if (config('log_visitor')){
                $ip=\Yuri2::getIp();
                if (!config('log_visitor_local') and $ip=='127.0.0.1'){
                    return ;
                }
                Factory::getLogger()->log(['id'=>ID,'url'=>url(),'session'=>$_SESSION],ID.' ---- 访问记录:'.$ip);
            }
        });
        $debugObj=Factory::getDebug();
        register_shutdown_function([$debugObj,'displayTrace']);//显示追踪按钮
        set_error_handler('naples\lib\ErrorCatch::onError');//错误处理
        set_exception_handler('naples\lib\ErrorCatch::onException');//异常处理
        register_shutdown_function('naples\lib\ErrorCatch::onShutdown');//关闭前处理
        register_shutdown_function([$debugObj,'saveDebug']);//保存debug信息
        register_shutdown_function(function (){
            //检查op cache （op_cache 可能引起代码不能及时更新）
            if (config('debug')){
                if (function_exists('opcache_reset')){
                    opcache_reset();
                }
            }
        });
    }
    
    /** 在控制器调度前的一些预处理工作 */
    private function onLoad(){
        Factory::getTimingProcess();
    }

}