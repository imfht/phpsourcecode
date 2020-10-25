<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 核心入口文件
 */

namespace onefox;

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('Require PHP > 5.4.0 !');
}

define('ONEFOX_VERSION', '2.2.3');
define('REQUEST_ID', uniqid());
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
!defined('APP_PATH') && die('APP_PATH is not defined');
!defined('ONEFOX_PATH') && die('ONEFOX_PATH is not defined');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);//目录分隔符
define('VENDOR_PATH', dirname(__DIR__) . DS . 'vendor'); //定义composer vendor目录
!defined('MODULE_MODE') && define('MODULE_MODE', true);//默认开启模块模式(Controller目录下含有子目录)
!defined('DEBUG') && define('DEBUG', false);//调试模式
!defined('LOG_PATH') && define('LOG_PATH', APP_PATH . DS . 'logs');//日志目录
!defined('CONF_PATH') && define('CONF_PATH', APP_PATH . DS . 'config');//配置目录
!defined('TPL_PATH') && define('TPL_PATH', APP_PATH . DS . 'tpl');//模板目录
!defined('LIB_PATH') && define('LIB_PATH', APP_PATH . DS . 'extend');//扩展类库目录
!defined('FUNC_PATH') && define('FUNC_PATH', APP_PATH . DS . 'function');//自定义函数库目录
!defined('FUNC_NAME') && define('FUNC_NAME', 'func.php');//自定义函数库文件名
!defined('DEFAULT_MODULE') && define('DEFAULT_MODULE', 'index');//默认执行模块
!defined('DEFAULT_CONTROLLER') && define('DEFAULT_CONTROLLER', 'Index');//默认执行控制器
!defined('DEFAULT_ACTION') && define('DEFAULT_ACTION', 'index');//默认执行方法
!defined('DEFAULT_TIMEZONE') && define('DEFAULT_TIMEZONE', 'Asia/Shanghai');//默认时区
!defined('XSS_MODE') && define('XSS_MODE', true);//开启XSS过滤
!defined('ADDSLASHES_MODE') && define('ADDSLASHES_MODE', false);//不使用addslashes

//引入框架函数库
require_once __DIR__ . DS . 'functions.php';

final class Onefox {

    private static $_ext = '.php';
    private static $_startTime = 0;
    private static $_memoryStart = 0;
    private static $_error;

    public static function start() {
        //--------设置时区--------//
        date_default_timezone_set(DEFAULT_TIMEZONE);

        //--------设置错误级别, 记录程序开始时间及内存--------//
        if (DEBUG) {
            ini_set('display_errors', 'On');
            error_reporting(E_ALL ^ E_NOTICE);
            self::$_startTime = microtime(true);
            self::$_memoryStart = memory_get_usage(true);
        }

        //--------自动注册类--------//
        spl_autoload_register([
            'OneFox\Onefox',
            'autoload'
        ]);

        //--------运行结束执行--------//
        register_shutdown_function([
            'OneFox\Onefox',
            'end'
        ]);

        //--------自定义错误处理--------//
        set_error_handler([
            'OneFox\Onefox',
            'errorHandler'
        ]);

        //--------处理未捕捉的异常--------//
        set_exception_handler([
            'OneFox\Onefox',
            'exceptionHandler'
        ]);

        //--------引入composer机制--------//
        if (is_dir(VENDOR_PATH) && is_file(VENDOR_PATH . DS . 'autoload.php')) {
            require VENDOR_PATH . DS . 'autoload.php';
        }

        //--------引入自定义的函数库--------//
        if (is_dir(FUNC_PATH) && is_file(FUNC_PATH . DS . FUNC_NAME)) {
            require_once FUNC_PATH . DS . FUNC_NAME;
        }

        //--------session设置--------//
        self::_initSession();

        if (!IS_CLI) {
            //--------处理请求数据--------//
            Request::deal();

            //--------简单路由--------//
            Dispatcher::dipatcher();

            //--------执行--------//
            self::_exec();
        }

        return;
    }

    public static function autoload($className) {
        $file = self::_parseClassPath($className);
        if (file_exists($file)) {
            require_once $file;
            return class_exists($className);
        }
        return false;
    }

    // 解析路径
    private static function _parseClassPath($className) {
        $class = $className;
        $path = strtr($class, '\\', DS);
        $file = null;
        //加载框架文件
        if (0 === strpos($class, 'onefox\\')) {
            $file = ONEFOX_PATH . substr($path, strlen('onefox')) . self::$_ext;
            return $file;
        }
        //加载应用目录下文件
        $file = APP_PATH . DS . $path . self::$_ext;
        if (file_exists($file)) {
            return $file;
        }
        //加载扩展库文件
        $file = LIB_PATH . DS . $path . self::$_ext;
        if (file_exists($file)) {
            return $file;
        }
        return $file;
    }

    private static function _exec() {
        define('CURRENT_MODULE', Dispatcher::getModuleName());
        define('CURRENT_CONTROLLER', Dispatcher::getControllerName());
        define('CURRENT_ACTION', Dispatcher::getActionName());
        $controllerName = CURRENT_CONTROLLER;
        $moduleName = CURRENT_MODULE;
        if ($moduleName) {
            $className = sprintf('controller\\%s\\%s', $moduleName, $controllerName);
        } else {
            $className = sprintf("controller\\%s", $controllerName);
        }
        //-----请求日志------//
        $params = [];
        $log = [
            'request_id' => REQUEST_ID,
            'uri' => $_SERVER['REQUEST_URI'],
            'class' => [
                'module' => CURRENT_MODULE,
                'controller' => CURRENT_CONTROLLER,
                'action' => CURRENT_ACTION
            ],
            'method' => Request::method(),
            'params' => array_merge($params, Request::gets(), Request::posts()),
            'stream' => Request::stream(),
            'cookie' => Request::cookies(),
            'ip' => Request::ip(),
        ];
        Log::info($log);
        if (!class_exists($className)) {
            throw new \RuntimeException('类不存在');
        }
        try {
            $obj = new \ReflectionClass($className);

            if ($obj->isAbstract()) {
                throw new \RuntimeException('抽象类不可被实例化');
            }

            $class = $obj->newInstance();

            //actions
            $property = $obj->getProperty('actions');
            $actions = $property->getValue($class);
            if (isset($actions[CURRENT_ACTION]) && !empty($actions[CURRENT_ACTION])) {
                $actionObj = new \ReflectionClass($actions[CURRENT_ACTION]);
                $actionClass = $actionObj->newInstance();
                if ($actionObj->hasMethod('excute')){
                    $execMethodObj = $actionObj->getMethod('excute');
                    if ($execMethodObj->isPublic() && !$execMethodObj->isStatic()) {
                        $execMethodObj->invoke($actionClass);
                    }
                }
            } else {
                //前置操作
                if ($obj->hasMethod(CURRENT_ACTION . 'Before')) {
                    $beforeMethod = $obj->getMethod(CURRENT_ACTION . 'Before');
                    if ($beforeMethod->isPublic() && !$beforeMethod->isStatic()) {
                        $beforeMethod->invoke($class);
                    }
                }

                $method = $obj->getMethod(CURRENT_ACTION . 'Action');
                if ($method->isPublic() && !$method->isStatic()) {
                    $method->invoke($class);
                }

                //后置操作
                if ($obj->hasMethod(CURRENT_ACTION . 'After')) {
                    $afterMethod = $obj->getMethod(CURRENT_ACTION . 'After');
                    if ($afterMethod->isPublic() && !$afterMethod->isStatic()) {
                        $afterMethod->invoke($class);
                    }
                }
            }
        } catch (\Exception $e) {
            self::_halt($e);
        }
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function exceptionHandler($e) {
        self::$_error = $e;
    }

    public static function end() {
        if (self::$_error) {
            $e = self::$_error;
            self::$_error = null;
            self::_halt($e);
        }
        //输出日志
        $log = [
            'request_id' => REQUEST_ID,
            'run_type' => IS_CLI ? 'cli' : 'web',
            'run_time' => number_format((microtime(true) - self::$_startTime) * 1000, 0) . 'ms',
            'run_memory' => number_format((memory_get_usage(true) - self::$_memoryStart) / (1024), 0, ",", ".") . 'kb'
        ];
        if (!IS_CLI) {
            $log['response'] = Response::getResponseData();
            $log['response_type'] = Response::getResponseType();
        }
        Log::info($log);
    }

    private static function _halt($e) {
        if (DEBUG) {
            if (IS_CLI) {
                exit(iconv('UTF-8', 'gbk', $e->getMessage()) . PHP_EOL . 'FILE: ' . $e->getFile() . '(' . $e->getLine() . ')' . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
            }
            include_once ONEFOX_PATH . DS . 'tpl' . DS . 'excetion.html';
        } else {
            $logError['url'] = $_SERVER['REQUEST_URI'];
            $logError['errmsg'] = $e->getMessage();
            $logError['file'] = $e->getFile();
            $logError['line'] = $e->getLine();
            Log::error($logError);
            if (IS_CLI) {
                exit();
            }
            $url = Config::get('404_page');
            if ($url) {
                Response::redirect($url);
            }
            header('HTTP/1.1 404 Not Found');
            header('Status:404 Not Found');
            include_once ONEFOX_PATH . DS . 'tpl' . DS . '404.html';
        }
    }

    // 初始化session
    private static function _initSession() {
        $sessionConf = Config::get('session');
        if (isset($sessionConf['auto_start']) && $sessionConf['auto_start']) {
            unset($sessionConf['auto_start']);
            if (isset($sessionConf['name']) && $sessionConf['name']) {
                session_name($sessionConf['name']);
                unset($sessionConf['name']);
            }
            if (isset($sessionConf['save_path']) && $sessionConf['save_path']) {
                session_save_path($sessionConf['save_path']);
                unset($sessionConf['save_path']);
            }
            if (isset($sessionConf['cache_limiter']) && $sessionConf['cache_limiter']) {
                session_cache_limiter($sessionConf['cache_limiter']);
                unset($sessionConf['cache_limiter']);
            }
            if (isset($sessionConf['cache_expire']) && $sessionConf['cache_expire']) {
                session_cache_expire($sessionConf['cache_expire']);
                unset($sessionConf['cache_expire']);
            }
            foreach ($sessionConf as $key => $val) {
                $sessionConf[$key] && ini_set('session.' . $key, $val);
            }
            session_start();
        }
    }
}

Onefox::start();
