<?php
/*********************************************************************************
 * TookPHP framework
 *-------------------------------------------------------------------------------
 * Homepage: http://www.19www.com
 * Copyright (c) 2015, http://19www.com. All Rights Reserved
 *-------------------------------------------------------------------------------
 * Author: lajox <lajox@19www.com>
 ********************************************************************************/

namespace Took;
final class TookPHP
{

    // 类映射
    private static $_map      = array();

    // 实例化对象
    private static $_instance = array();

    /**
     * 初始化常量定义
     * @access private
     * @return void
     */
    static private function initDefine()
    {
        defined('ROOT_PATH')                        or define('ROOT_PATH', str_replace('\\','/',dirname( str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME'])).'/') ); //根目录
        defined('DS')                               or define("DS",                     DIRECTORY_SEPARATOR); //目录分隔符
        defined('IS_CGI')                           or define('IS_CGI',                 substr(PHP_SAPI, 0, 3) == 'cgi' ? TRUE : FALSE);
        defined('IS_WIN')                           or define('IS_WIN',                 strstr(PHP_OS, 'WIN') ? TRUE : FALSE);
        defined('IS_CLI')                           or define('IS_CLI',                 PHP_SAPI == 'cli' ? TRUE : FALSE);
        defined('TOOK_DATA_PATH')                   or define("TOOK_DATA_PATH",         TOOK_PATH . 'Data/'); //数据目录
        defined('TOOK_LIB_PATH')                    or define("TOOK_LIB_PATH",          TOOK_PATH . 'Library/'); //lib目录
        defined('TOOK_CONFIG_PATH')                 or define("TOOK_CONFIG_PATH",       TOOK_PATH . 'Config/'); //配置目录
        defined('TOOK_CORE_PATH')                   or define("TOOK_CORE_PATH",         TOOK_LIB_PATH . 'Took/'); //核心目录
        defined('TOOK_ORG_PATH')                    or define("TOOK_ORG_PATH",          TOOK_LIB_PATH . 'Org/'); //Org目录
        defined('TOOK_VENDOR_PATH')                 or define("TOOK_VENDOR_PATH",       TOOK_LIB_PATH . 'Vendor/'); //第三方类库目录
        defined('TOOK_TOOL_PATH')                   or define("TOOK_TOOL_PATH",         TOOK_LIB_PATH . 'Tool/'); //工具包
        defined('TOOK_FUNCTION_PATH')               or define("TOOK_FUNCTION_PATH",     TOOK_LIB_PATH . 'Function/'); //系统函数目录
        defined('TOOK_TPL_PATH')                    or define("TOOK_TPL_PATH",          TOOK_PATH . 'Tpl/'); //框架Tpl目录
        defined('TOOK_LANG_PATH')                   or define("TOOK_LANG_PATH",         TOOK_PATH . 'Lang/'); //语言目录
        defined('STATIC_PATH')                      or define("STATIC_PATH", 'Static/'); //网站静态文件目录
        defined('APP_COMMON_PATH')                  or define("APP_COMMON_PATH", APP_PATH. 'Common/'); //应用公共目录
        defined('APP_ADDON_PATH')                   or define("APP_ADDON_PATH", APP_PATH . 'Addons/' ); //插件目录
        defined('COMMON_CONFIG_PATH')               or define("COMMON_CONFIG_PATH", APP_COMMON_PATH . 'Config/' ); //公共模块配置目录
        defined('COMMON_MODEL_PATH')                or define("COMMON_MODEL_PATH",  APP_COMMON_PATH . 'Model/' ); //公共模块模型目录
        defined('COMMON_CONTROLLER_PATH')           or define("COMMON_CONTROLLER_PATH",  APP_COMMON_PATH . 'Controller/'); //公共模块控制器目录
        defined('COMMON_LANG_PATH')                 or define("COMMON_LANG_PATH", APP_COMMON_PATH . 'Lang/'); //公共模块语言包目录
        defined('COMMON_HOOK_PATH')                 or define("COMMON_HOOK_PATH", APP_COMMON_PATH . 'Hook/' ); //公共模块钓子目录
        defined('COMMON_TAG_PATH')                  or define("COMMON_TAG_PATH",  APP_COMMON_PATH . 'Tag/'); //公共模块标签目录
        defined('COMMON_LIB_PATH')                  or define("COMMON_LIB_PATH", APP_COMMON_PATH . 'Library/' ); //公共模块扩展包目录
        defined('COMMON_FUNCTION_PATH')             or define("COMMON_FUNCTION_PATH", APP_COMMON_PATH . 'Function/' ); //公共模块扩展函数目录
        defined('TEMP_COMPILE_PATH')                or define("TEMP_COMPILE_PATH", TEMP_PATH . 'Compile/' ); //应用编译包目录
        defined('TEMP_CACHE_PATH')                  or define("TEMP_CACHE_PATH", TEMP_PATH . 'Cache/' ); //应用缓存目录
        defined('TEMP_TABLE_PATH')                  or define("TEMP_TABLE_PATH", TEMP_PATH . 'Table/' ); //表字段缓存
        defined('TEMP_LOG_PATH')                    or define("TEMP_LOG_PATH", TEMP_PATH . 'Log/' ); //应用日志目录
        defined('CONF_EXT')                         or define('CONF_EXT', '.php'); // 配置文件后缀
    }

    /**
     * 运行框架 run方法
     * @access public
     * @return void
     */
    static public function run()
    {
        //初始化常量定义
        self::initDefine();
        //加载核心文件
        self::loadCoreFile();
        //加载基本配置
        self::loadConfig();
        //编译核心文件
        self::compile();
        //应用初始化
        self::init();
        //创建应用目录
        self::mkDirs();
        //运行应用
        App::run();
    }

    // 注册classmap
    static public function addMap($class, $map=''){
        if(is_array($class)){
            self::$_map = array_merge(self::$_map, $class);
        }else{
            self::$_map[$class] = $map;
        }
    }

    // 获取classmap
    static public function getMap($class=''){
        if(''===$class){
            return self::$_map;
        }elseif(isset(self::$_map[$class])){
            return self::$_map[$class];
        }else{
            return null;
        }
    }

    /**
     * 取得对象实例 支持调用类的静态方法
     * @param string $class 对象类名
     * @param string $method 类的静态方法名
     * @return object
     */
    static public function instance($class,$method='') {
        $identify   =   $class.$method;
        if(!isset(self::$_instance[$identify])) {
            if(class_exists($class)){
                $o = new $class();
                if(!empty($method) && method_exists($o,$method))
                    self::$_instance[$identify] = call_user_func(array(&$o, $method));
                else
                    self::$_instance[$identify] = $o;
            }
            else
                halt('实例化一个不存在的类:'.$class);
        }
        return self::$_instance[$identify];
    }

    /**
     * 加载核心文件
     * @access private
     * @return void
     */
    static private function loadCoreFile()
    {
        require_once(TOOK_FUNCTION_PATH . 'Functions.php'); //系统预定义函数
        $files = array(
            TOOK_CORE_PATH . 'Controller'.EXT, //TookPHP顶级类
            TOOK_CORE_PATH . 'Exception'.EXT, //异常处理类
            TOOK_CORE_PATH . 'App'.EXT, //TookPHP顶级类
            TOOK_CORE_PATH . 'Route'.EXT, //URL处理类
            TOOK_CORE_PATH . 'Hook'.EXT, //钓子处理类
            TOOK_CORE_PATH . 'Log'.EXT, //公共函数
            TOOK_CORE_PATH . 'Debug'.EXT, //Debug处理类
        );
        foreach ($files as $v) {
            require_cache($v);
        }
    }

    /**
     * 加载基本配置
     * @access private
     */
    static private function loadConfig()
    {
        //系统配置
        C(require(TOOK_CONFIG_PATH . 'config'.CONF_EXT));
        //系统语言
        L(require(TOOK_LANG_PATH . strtolower(C('DEFAULT_LANG')).'.php'));
        //应用别名导入
        alias_import(C('ALIAS'));
    }
    /**
     * 创建项目运行目录
     * @access private
     * @return void
     */
    static public function mkDirs()
    {
        if (is_dir(APP_COMMON_PATH) && !is_empty_dir(APP_COMMON_PATH)) return;
        //目录
        $dirs = array(
            APP_PATH,
            //临时目录
            TEMP_PATH,
            //应用组目录
            APP_COMMON_PATH,
            APP_MODULE_PATH,
            APP_ADDON_PATH,
            //公共模块目录
            COMMON_CONFIG_PATH,
            COMMON_MODEL_PATH,
            COMMON_LANG_PATH,
            COMMON_CONTROLLER_PATH,
            COMMON_HOOK_PATH,
            COMMON_TAG_PATH,
            COMMON_FUNCTION_PATH,
            COMMON_LIB_PATH,
            //编译目录
            TEMP_COMPILE_PATH,
            TEMP_CACHE_PATH,
            TEMP_TABLE_PATH,
            TEMP_LOG_PATH,
            //模块目录
            MODULE_CONFIG_PATH,
            MODULE_MODEL_PATH,
            MODULE_LANG_PATH,
            MODULE_CONTROLLER_PATH,
            MODULE_HOOK_PATH,
            MODULE_TAG_PATH,
            MODULE_LIB_PATH,
            MODULE_FUNCTION_PATH,
            MODULE_VIEW_PATH,
            //控制器目录
            MODULE_VIEW_CONTROLLER_PATH,
            MODULE_VIEW_PUBLIC_PATH,
            //公共目录
            STATIC_PATH
        );
        foreach ($dirs as $d) {
            if (!dir_create($d, 0755)) {
                header("Content-type:text/html;charset=utf-8");
                exit("目录{$d}创建失败，请检查权限");
            }
        }
        $files = array(
            //复制视图
            TOOK_TPL_PATH . 'view.html' => MODULE_VIEW_CONTROLLER_PATH . 'index.html',
            //复制模板文件
            TOOK_TPL_PATH . 'success.html' => MODULE_VIEW_PUBLIC_PATH . 'success.html',
            TOOK_TPL_PATH . 'error.html' => MODULE_VIEW_PUBLIC_PATH . 'error.html',
            //复制配置文件
            TOOK_LIB_PATH . 'Data/configApp.php' => COMMON_CONFIG_PATH . 'config'.CONF_EXT,
            TOOK_LIB_PATH . 'Data/configModule.php' => MODULE_CONFIG_PATH . 'config'.CONF_EXT,
            //复制自定义函数文件
            TOOK_LIB_PATH . 'Data/function.php' => array(
                COMMON_FUNCTION_PATH . 'function.php',
                MODULE_FUNCTION_PATH . 'function.php',
            ),
            //复制自定义函数文件
            TOOK_LIB_PATH . 'Data/zh-cn.php' => array(
                COMMON_LANG_PATH . 'zh-cn.php',
                MODULE_LANG_PATH . 'zh-cn.php',
            ),
            //创建测试控制器
            TOOK_LIB_PATH . 'Data/IndexController'.EXT => MODULE_CONTROLLER_PATH . "IndexController".EXT,
        );
        copy_files($files);
        //创建安全文件
        self::safeFile();
    }

    /**
     * 创建安全文件
     * @access private
     * @return void
     */
    static private function safeFile()
    {
        if (defined("DIR_SAFE") && DIR_SAFE===false) return;
        $dirs = array(
            APP_PATH,
            //临时目录
            TEMP_PATH,
            TEMP_COMPILE_PATH,
            TEMP_CACHE_PATH,
            TEMP_TABLE_PATH,
            TEMP_LOG_PATH,
            //应用组目录
            APP_COMMON_PATH,
            APP_ADDON_PATH,
            APP_MODULE_PATH,
            //公共模块目录
            COMMON_CONFIG_PATH,
            COMMON_MODEL_PATH,
            COMMON_CONTROLLER_PATH,
            COMMON_LANG_PATH,
            COMMON_HOOK_PATH,
            COMMON_TAG_PATH,
            COMMON_LIB_PATH,
            COMMON_FUNCTION_PATH,
            //模块目录
            MODULE_CONFIG_PATH,
            MODULE_MODEL_PATH,
            MODULE_CONTROLLER_PATH,
            MODULE_LANG_PATH,
            MODULE_HOOK_PATH,
            MODULE_TAG_PATH,
            MODULE_LIB_PATH,
            MODULE_FUNCTION_PATH,
            MODULE_VIEW_PATH,
            //公共目录
            STATIC_PATH
        );
        $file = TOOK_TPL_PATH . 'index.html';
        foreach ($dirs as $d) {
            is_file($d . '/index.html') || !is_dir($d) || @copy($file, $d . '/index.html');
        }
    }

    /**
     * 编译核心文件
     * @access private
     */
    static private function compile()
    {
        if (DEBUG) {
            is_file(TEMP_FILE) and unlink(TEMP_FILE);
            return;
        }
        $compile = 'namespace {';
        //常量编译
        $_define = get_defined_constants(true);
        foreach ($_define['user'] as $n => $d) {
            if ($d == '\\') $d = "'\\\\'";
            else $d = is_int($d) ? intval($d) : "'{$d}'";
            $compile .= "defined('{$n}') OR define('{$n}',{$d});";
        }
        $compile = $compile."}";
        $filedata = [];
        $files = array(
            TOOK_CORE_PATH . 'App'.EXT, //TookPHP顶级类
            TOOK_CORE_PATH . 'Controller'.EXT, //控制器基类
            TOOK_CORE_PATH . 'Debug'.EXT, //Debug处理类
            TOOK_CORE_PATH . 'Hook'.EXT, //事件处理类
            TOOK_CORE_PATH . 'TookPHP'.EXT, //TookPHP顶级类
            TOOK_CORE_PATH . 'Exception'.EXT, //异常处理类
            TOOK_CORE_PATH . 'Log'.EXT, //Log日志类
            TOOK_CORE_PATH . 'Route'.EXT, //URL处理类
            TOOK_CORE_PATH . 'Cache'.EXT, //缓存基类
            TOOK_CORE_PATH . 'Cache/CacheFactory'.EXT, //缓存工厂类
            TOOK_CORE_PATH . 'Db/Db'.EXT, //数据处理基类
            TOOK_CORE_PATH . 'Db/DbFactory'.EXT, //数据工厂类
            TOOK_CORE_PATH . 'Db/DbInterface'.EXT, //数据接口类
            TOOK_CORE_PATH . 'Model'.EXT, //模型基类
            TOOK_CORE_PATH . 'Model/RelationModel'.EXT, //关联模型类
            TOOK_CORE_PATH . 'Model/ViewModel'.EXT, //视图模型类
            TOOK_CORE_PATH . 'View/ViewFactory'.EXT, //视图工厂库
            TOOK_CORE_PATH . 'View'.EXT, //模板编译类
            TOOK_LIB_PATH . 'Tool/Dir'.EXT, //目录操作类
        );
        array_unshift($files, TOOK_FUNCTION_PATH . 'Functions.php'); //预定义函数
        foreach ($files as $f) {
            $content = trim(substr(php_strip_whitespace($f), 5));
            //$content = trim(substr(file_get_contents($f), 5));
            $content = compress($content);
            if(0===strpos($content,'namespace')){
                $content    =   preg_replace('/namespace\s(.*?);/','namespace \\1{',$content,1);
            }else{
                $content    =   'namespace {'.$content;
            }
            if ('?>' == substr($content, -2))
                $content    = substr($content, 0, -2);
            $content = $content.'}';
            $filedata[$f] = $content;
        }
        $compile .= implode("\n",$filedata)."";
        //编译内容
        $compile = '<?php ' . $compile . '';
        //$compile = compress($compile);
        //创建runtime编译文件
        if (is_dir(TEMP_PATH) or dir_create(TEMP_PATH) and is_writable(TEMP_PATH)) {
            return file_put_contents(TEMP_FILE, $compile);
        }
        else {
            //exit("请修改" . realpath(TEMP_PATH) . "目录权限");
        }
    }

    /**
     * 初始化应用
     */
    static public function init()
    {
        //加载基本配置
        self::loadConfig();
        //加载应用配置
        is_file(COMMON_CONFIG_PATH . 'config'.CONF_EXT)  and C(require(COMMON_CONFIG_PATH . 'config'.CONF_EXT));
        is_file(COMMON_LANG_PATH . strtolower(C('DEFAULT_LANG')).'.php')  and L(require COMMON_LANG_PATH . strtolower(C('DEFAULT_LANG')).'.php');

        // 加载动态应用公共文件和配置
        load_ext_file(APP_COMMON_PATH);

        //解析路由
        Route::parseUrl();

        //常量定义
        if(!defined('APP_MODULE_PATH')){
            if(empty($_GET[C('VAR_GROUP')])){
                //普通模块
                define('APP_MODULE_PATH',APP_PATH.MODULE.'/');
            }else if($_GET[C('VAR_GROUP')]=='Addon'){
                //插件模块
                define('APP_MODULE_PATH',APP_ADDON_PATH.MODULE.'/');
            }else{
                //根据应用组目录识别模块
                define('APP_MODULE_PATH',APP_PATH.$_GET[C('VAR_GROUP')].'/'.MODULE.'/');
            }
        }

        defined('MODULE_CONTROLLER_PATH')                       or define('MODULE_CONTROLLER_PATH', APP_MODULE_PATH . 'Controller/');
        defined('MODULE_MODEL_PATH')                            or define('MODULE_MODEL_PATH', APP_MODULE_PATH . 'Model/');
        defined('MODULE_CONFIG_PATH')                           or define('MODULE_CONFIG_PATH', APP_MODULE_PATH . 'Config/');
        defined('MODULE_HOOK_PATH')                             or define('MODULE_HOOK_PATH', APP_MODULE_PATH . 'Hook/');
        defined('MODULE_LANG_PATH')                             or define('MODULE_LANG_PATH', APP_MODULE_PATH . 'Lang/');
        defined('MODULE_TAG_PATH')                              or define('MODULE_TAG_PATH', APP_MODULE_PATH . 'Tag/');
        defined('MODULE_LIB_PATH')                              or define('MODULE_LIB_PATH', APP_MODULE_PATH . 'Library/');
        defined('MODULE_FUNCTION_PATH')                         or define('MODULE_FUNCTION_PATH', APP_MODULE_PATH . 'Function/');
        //应用配置
        is_file(MODULE_CONFIG_PATH . 'config'.CONF_EXT)         and C(require(MODULE_CONFIG_PATH . 'config'.CONF_EXT));
        is_file(MODULE_LANG_PATH . strtolower(C('DEFAULT_LANG')).'.php')      and L(require MODULE_LANG_PATH . strtolower(C('DEFAULT_LANG')).'.php');
        // 加载动态应用文件和配置, 自定义函数
        is_file(COMMON_FUNCTION_PATH.'function.php')            and require_once(COMMON_FUNCTION_PATH.'function.php');
        is_file(MODULE_FUNCTION_PATH.'function.php')            and require_once(MODULE_FUNCTION_PATH.'function.php');
        load_ext_file(APP_MODULE_PATH);
        //模板目录常量
        defined('MODULE_VIEW_PATH')                             or define('MODULE_VIEW_PATH',strstr(C('TPL_PATH'),'/')?C('TPL_PATH').C('TPL_STYLE'): APP_MODULE_PATH.C('TPL_PATH').'/'.C('TPL_STYLE'));
        defined('MODULE_VIEW_PUBLIC_PATH')                      or define('MODULE_VIEW_PUBLIC_PATH', MODULE_VIEW_PATH .'Public/');
        defined('MODULE_VIEW_CONTROLLER_PATH')                  or define('MODULE_VIEW_CONTROLLER_PATH',MODULE_VIEW_PATH.CONTROLLER.'/');
        //网站根-Static目录
        defined("__STATIC__")                                   or define('__STATIC__', __ROOT__ . '/Static');
        defined('__TOOK_TPL__')                                 or define('__TOOK_TPL__', __TOOK__.'/Tpl');
        defined("__VIEW__")                                     or define('__VIEW__', __ROOT__  . '/'.str_replace('\\','/',substr(realpath(realpath(MODULE_VIEW_PATH)),strlen(realpath(dirname(APP_PATH)))+1)));
        defined("__PUBLIC__")                                   or define('__PUBLIC__', __VIEW__ . '/Public');
        defined("__CONTROLLER_VIEW__")                          or define('__CONTROLLER_VIEW__', __VIEW__  .'/'. CONTROLLER);
        //来源URL
        define("__REFERER__",                                   isset($_SERVER["HTTP_REFERER"])?$_SERVER["HTTP_REFERER"]:null);
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
        //禁止使用模块检测
        $module_on = defined('MODULE_LIST') ? string_to_array(MODULE_LIST) : string_to_array(C('MODULE_LIST'));
        $module_off = defined('DENY_MODULE') ? string_to_array(DENY_MODULE) : string_to_array(C('DENY_MODULE'));
        (in_array_case(MODULE,$module_off) || ($module_on && !in_array_case(MODULE,$module_on))) && halt(MODULE.'模块禁止使用');
        //导入钓子
        Hook::import(C('HOOK'));
        //模块导入
        alias_import(C('ALIAS'));
        //注册自动载入函数
        spl_autoload_register(array(__CLASS__,                  'autoload'));
        set_error_handler(array(__CLASS__,                      'error'), E_ALL);
        set_exception_handler(array(__CLASS__,                  'exception'));
        register_shutdown_function(array(__CLASS__,             'fatalError'));
    }

    /**
     * 自动载入函数
     * @param string $className 类名
     * @access private
     * @return void
     */
    static public function autoload($className)
    {
        $className = str_replace('\\', '/', $className);
        $class = $className . EXT; //类文件
        // 检查是否存在映射
        if(isset(self::$_map[$className]) &&
            require_array(array(
                    self::$_map[$className]
                )
            )) {
            return ;
        }
        elseif (alias_import($className)) {
            return;
        }
        elseif(false !== strpos($className,'/')){
            $name = strstr($className, '/', true);
            // 核心目录下：Took、 Tool
            if ((in_array($name, array(basename(TOOK_CORE_PATH), basename(TOOK_TOOL_PATH)))) &&
                require_array(array(
                    TOOK_LIB_PATH . '' . $class
                ))) {
                return;
            }
            // 应用目录下：Common
            elseif ($name == basename(APP_COMMON_PATH) &&
                require_array(array(
                    APP_PATH . '' . $class
                ))) {
                return;
            }
            else {
                // 检测自定义命名空间 否则就以模块为命名空间
                $namespace = C('APP_AUTOLOAD_NAMESPACE');
                $path = isset($namespace[$name])? dirname($namespace[$name]).'/' : APP_PATH;
                if($namespace && require_cache($path.''.$class)) {
                    return;
                }
                // 根据自动加载路径设置进行尝试搜索
                $paths = C('APP_AUTOLOAD_PATH');
                $paths = array_filter(is_array($paths) ? $paths : explode(',',$paths));

                foreach ($paths as $path){
                    $path = trim(str_replace('.','/',$path));
                    $path = rtrim($path,'.');
                    if(import($path.'/'.$className))
                        // 如果加载类成功则返回
                        return;
                }
            }
        }
        if (require_array([
                TOOK_CORE_PATH . $class,
                TOOK_LIB_PATH . $class,
                MODULE_LIB_PATH . $class,
                COMMON_LIB_PATH . $class,
                APP_MODULE_PATH . $class,
                APP_COMMON_PATH . $class,
                APP_PATH . $class,
            ]) || require_cache($class)) {
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
                $msg = $error.' in '. $file . " 第 $line 行.";
                if(C('LOG_RECORD')) Log::write("[$errno] " . $msg, Log::ERROR);
                function_exists('halt') ? halt($msg) : exit('ERROR:' . $msg);
                break;
            default:
                $errorStr = "[$errno] $error " . $file . " 第 $line 行.";
                trace($errorStr, 'NOTICE', true);
                //SHOW_NOTICE开启提示信息
                if (DEBUG && C('SHOW_NOTICE'))
                    require TOOK_TPL_PATH . 'notice.html';
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