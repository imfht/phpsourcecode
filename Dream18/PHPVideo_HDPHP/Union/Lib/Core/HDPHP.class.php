<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
final class HDPHP
{
    /**
     * 初始化应用
     */
    static public function init()
    {
        //加载应用配置
        is_file(APP_CONFIG_PATH . 'config.php')                 and C(require(APP_CONFIG_PATH . 'config.php'));
        is_file(APP_LANGUAGE_PATH . C('LANGUAGE') . '.php')     and L(require APP_LANGUAGE_PATH . C('LANGUAGE') . '.php');
        //解析路由
        Route::parseUrl();
        //导入钓子
        Hook::import(C('HOOK'));
        //禁止使用模块检测
        in_array(MODULE,C('DENY_MODULE')) && halt(MODULE.'模块禁止使用');
        //常量定义
        if(!defined('MODULE_PATH')){
            if(empty($_GET[C('VAR_GROUP')])){
                //普通模块
                define('MODULE_PATH',APP_PATH.MODULE.'/');
            }else if($_GET[C('VAR_GROUP')]=='Addon'){
                //插件模块
                define('MODULE_PATH',APP_ADDON_PATH.MODULE.'/');
            }else{
                //根据应用组目录识别模块
                define('MODULE_PATH',APP_PATH.$_GET[C('VAR_GROUP')].'/'.MODULE.'/');
            }
        }
        defined('MODULE_CONTROLLER_PATH')                       or define('MODULE_CONTROLLER_PATH', MODULE_PATH . 'Controller/');
        defined('MODULE_MODEL_PATH')                            or define('MODULE_MODEL_PATH', MODULE_PATH . 'Model/');
        defined('MODULE_CONFIG_PATH')                           or define('MODULE_CONFIG_PATH', MODULE_PATH . 'Config/');
        defined('MODULE_HOOK_PATH')                             or define('MODULE_HOOK_PATH', MODULE_PATH . 'Hook/');
        defined('MODULE_LANGUAGE_PATH')                         or define('MODULE_LANGUAGE_PATH', MODULE_PATH . 'Language/');
        defined('MODULE_TAG_PATH')                              or define('MODULE_TAG_PATH', MODULE_PATH . 'Tag/');
        defined('MODULE_LIB_PATH')                              or define('MODULE_LIB_PATH', MODULE_PATH . 'Lib/');
        //应用配置
        is_file(MODULE_CONFIG_PATH . 'config.php')              and C(require(MODULE_CONFIG_PATH . 'config.php'));
        is_file(MODULE_LANGUAGE_PATH . C('LANGUAGE') . '.php')  and L(require MODULE_LANGUAGE_PATH . C('LANGUAGE') . '.php');
        //模板目录常量
        defined('MODULE_VIEW_PATH')                             or define('MODULE_VIEW_PATH',strstr(C('TPL_PATH'),'/')?C('TPL_PATH').C('TPL_STYLE'):
            MODULE_PATH.C('TPL_PATH').'/'.C('TPL_STYLE'));
        defined('MODULE_PUBLIC_PATH')                           or define('MODULE_PUBLIC_PATH', MODULE_VIEW_PATH .'Public/');
        defined('CONTROLLER_VIEW_PATH')                         or define('CONTROLLER_VIEW_PATH',MODULE_VIEW_PATH.CONTROLLER.'/');
        //网站根-Static目录
        defined("__STATIC__")                                   or define('__STATIC__', __ROOT__ . '/Static');
        defined('__HDPHP_TPL__')                                or define('__HDPHP_TPL__',__HDPHP__.'/Lib/Tpl');
        defined("__VIEW__")                                     or define('__VIEW__', __ROOT__  . '/'.rtrim(MODULE_VIEW_PATH,'/'));
        defined("__PUBLIC__")                                   or define('__PUBLIC__', __VIEW__ . '/Public');
        defined("__CONTROLLER_VIEW__")                          or define('__CONTROLLER_VIEW__', __VIEW__  .'/'. CONTROLLER);
        //来源URL
        define("__HISTORY__",                                   isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:null);
        //=========================环境配置
        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
        @ini_set('memory_limit',                                '128M');
        @ini_set('register_globals',                            'off');
        @ini_set('magic_quotes_runtime',                        0);
        define('NOW',                                           $_SERVER['REQUEST_TIME']);
        define('NOW_MICROTIME',                                 microtime(true));
        define('REQUEST_METHOD',                                $_SERVER['REQUEST_METHOD']);
        define('IS_GET',                                        REQUEST_METHOD == 'GET' ? true : false);
        define('IS_POST',                                       REQUEST_METHOD == 'POST' ? true : false);
        define('IS_PUT',                                        REQUEST_METHOD == 'PUT' ? true : false);
        define('IS_AJAX',                                       ajax_request());
        define('IS_DELETE',                                     REQUEST_METHOD == 'DELETE' ? true : false);
        //模块导入
        alias_import(C('ALIAS'));
        //注册自动载入函数
        spl_autoload_register(array(__CLASS__,                  'autoload'));
        set_error_handler(array(__CLASS__,                      'error'), E_ALL);
        set_exception_handler(array(__CLASS__,                  'exception'));
        register_shutdown_function(array(__CLASS__,             'fatalError'));
        HDPHP::autoLoadFile();
    }
    /**
     * 自动加载文件
     */
    static private function autoLoadFile()
    {
        //自动加载文件列表
        $files = C('AUTO_LOAD_FILE');
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                require_array(array(
                    MODULE_LIB_PATH . $file,
                    APP_LIB_PATH . $file
                )) || require_cache($file);
            }
        }
    }

    /**
     * 自动载入函数
     * @param string $className 类名
     * @access private
     * @return void
     */
    static public function autoload($className)
    {
        $class = ucfirst($className) . '.class.php'; //类文件
        if (substr($className, -5) == 'Model' && require_array(array(
                HDPHP_DRIVER_PATH . 'Model/' . $class,
                MODULE_MODEL_PATH . $class,
                APP_MODEL_PATH . $class
            ))) {return;
        } elseif (substr($className, -10) == 'Controller' && require_array(array(
                HDPHP_CORE_PATH . $class,
                MODULE_CONTROLLER_PATH . $class,
                APP_CONTROLLER_PATH . $class
            ))) {return;
        } elseif (substr($className, 0, 2) == 'Db' && require_array(array(
                HDPHP_DRIVER_PATH . 'Db/' . $class
            ))) { return;
        } elseif (substr($className, 0, 5) == 'Cache' && require_array(array(
                HDPHP_DRIVER_PATH . 'Cache/' . $class
            ))) {return;
        } elseif (substr($className, 0, 4) == 'View' && require_array(array(
                HDPHP_DRIVER_PATH . 'View/' . $class,
            ))) {return;
        } elseif (substr($className, -4) == 'Hook' && require_array(array(
                MODULE_HOOK_PATH  . $class,
                APP_HOOK_PATH  . $class
            ))) {return;
        } elseif (substr($className, -5) == 'Addon' && require_array(array(
                APP_ADDON_PATH  . $class
            ))) {return;
        } elseif (substr($className, -3) == 'Tag' && require_array(array(
                APP_TAG_PATH . $class,
                MODULE_TAG_PATH . $class
            ))) { return;
        } elseif (substr($className, -7) == 'Storage' && require_array(array(
                HDPHP_DRIVER_PATH . 'Storage/' . $class
            ))) {return;
        } elseif (alias_import($className)) {
            return;
        } elseif (require_array(array(
            MODULE_LIB_PATH . $class,
            APP_LIB_PATH . $class,
            HDPHP_CORE_PATH . $class,
            HDPHP_EXTEND_PATH . '/Tool/' . $class
        ))
        ) {
            return;
        }
    }

    /**
     * 自定义异常理
     * @param $e
     */
    static public function exception($e)
    {
        halt($e->__toString());
    }

    //错误处理
    static public function error($errno, $error, $file, $line)
    {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $msg = $error. $file . " 第 $line 行.";
                if(C('LOG_RECORD')) Log::write("[$errno] " . $msg, Log::ERROR);
                function_exists('halt') ? halt($msg) : exit('ERROR:' . $msg);
                break;
            default:
                $errorStr = "[$errno] $error " . $file . " 第 $line 行.";
                trace($errorStr, 'NOTICE', true);
                //SHUT_NOTICE关闭提示信息
                if (DEBUG && C('SHOW_NOTICE'))
                    require HDPHP_PATH . 'Lib/Tpl/notice.html';
                break;
        }
    }

    //致命错误处理
    static public function fatalError()
    {
        if(function_exists('error_get_last')){
            if ( $e = error_get_last()) {
                self::error($e['type'], $e['message'], $e['file'], $e['line']);
            }
        }
    }
}