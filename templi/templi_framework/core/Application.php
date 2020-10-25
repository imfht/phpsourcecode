<?php
/**
 * 应用抽象类
 * User: 七觞酒
 * Date: 14-5-9
 * Time: 下午4:16
 */

namespace framework\core;

use framework\session\Session;
use framework\cache\Cache;


require_once __DIR__.'/ApplicationInterface.php';
require_once __DIR__.'/LoaderInterface.php';
require_once __DIR__.'/Object.php';
/**
 * Class Application
 * @package framework\core
 * @property \framework\core\Router $router
 * @property \framework\core\Request $request
 * @property \framework\session\Session $session
 */
class Application extends Object implements ApplicationInterface, LoaderInterface
{

    /** @var Controller  $controller 当前控制器对象*/
    public $controller = null;

    /** @var string 应用名称 */
    public $appName = null;

    /** @var string app dir */
    public $appPath = null;

    /** @var Application  */
    private static $_app = null;

    /** @var array 命名空间规则*/
    private static $_nameSpaceRules = [];

    /**@var array 应用配置 */
    private static $_config = [];

    /**
     * 构造方法
     * @param array $config
     */
    public function __construct(array $config=[])
    {
        self::$_app = $this;
        $this->appName = $config['app_name'];
        $this->appPath = $config['app_path'];
        $this->getConfig($config);
        $this->init();
    }

    /**
     * 初始化app
     */
    protected function init()
    {
        //当前时间
        defined('SYS_TIME') or define('SYS_TIME', time());
        //系统脚本开始时间
        defined('SYS_START_TIME') or define('SYS_START_TIME', microtime(true));
        //TEMPLI 目录
        defined('TEMPLI_PATH') or  define('TEMPLI_PATH',dirname(__DIR__).DIRECTORY_SEPARATOR);

        switch($this->getConfig('run_mode')){
            case 'development':
                define('ERROR_TYPE', E_ALL & ~E_NOTICE);
                defined('APP_DEBUG') or define('APP_DEBUG',true);
                error_reporting(ERROR_TYPE);
                break;
            case 'testing':
            case 'production':
                define('ERROR_TYPE', 0);
                defined('APP_DEBUG') or define('APP_DEBUG',false);
                error_reporting(0);
                break;
            default:
                exit('项目 run_mode 配置错误');
        }

        $this->registerNameSpaceRule('framework', TEMPLI_PATH);
        $this->registerNameSpaceRule($this->appName, $this->appPath);
        spl_autoload_register('self::autoload', true);

        //自定义异常处理
        if(is_callable([$this, 'appException']) && function_exists('set_exception_handler')){
            set_exception_handler([$this,'appException']);
        }
        //自定义错误处理
        if(is_callable([$this, 'appError']) && function_exists('set_error_handler')){
            set_error_handler([$this,'appError'], ERROR_TYPE);
        }
    }
    /**
     * 获取 当前应用
     * @return Application
     */
    public static function getApp()
    {
        return self::$_app;
    }

    /**
     * 自动加载 类文件 包括 database、controller、libraries 类
     *
     * @param string $class
     * @throws Abnormal
     */
    public static function autoload($class)
    {
        $class = ltrim($class, '\\');
        $nameSpace = substr($class, 0, strpos($class, '\\'));
        if(isset(self::$_nameSpaceRules[$nameSpace])){
            $class = str_replace('\\', '/', $class);
            $file = str_replace($nameSpace, rtrim(self::$_nameSpaceRules[$nameSpace],'\\/'), $class).'.php';
            if(file_exists($file)){
                require $file;
            }else{
                throw new Abnormal($file.'文件不存在');
            }
        }

    }

    /**
     * 注册命名空间规则
     * @param string $nameSpace
     * @param string $path
     * @return bool
     */
    public function registerNameSpaceRule($nameSpace, $path)
    {
        return self::$_nameSpaceRules[$nameSpace] = $path;
    }

    /**
     * 获取版本信息
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * 获取配置文件信息
     * $field 为空时 获取全部配置信息
     * $field 为字符串时 返回当前 索引 配置值
     * $field 为数组时 设置配置信息
     * @param string $field
     * @param mixed  $default
     * @return mixed
     */
    public function getConfig($field = NULL, $default = NULL)
    {
        //设置配置信息
        if(is_array($field)){
            self::$_config = array_merge(self::$_config, $field);
        }
        if(is_string($field)){
            //return isset(self::$_config[$field])? self::$_config[$field]:$default;
            return self::getArrVal(self::$_config, $field, $default);
        }
        return self::$_config;
    }
    /**
     * 获取 数组中元素的值
     * @param array $arr
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getArrVal(array $arr, $key, $default = NULL)
    {

        $temp =  explode('.', $key);
        $myKey = $temp[0];

        if (!isset($arr[$myKey])){
            return $default;
        }

        if(isset($temp[1])) {
            array_shift($temp);
            $temp = implode('.', $temp);
            return self::getArrVal($arr[$myKey], $temp, $default);
        }
        return $arr[$myKey];
    }

    /**
     * 初始化函数
     */
    public function run()
    {
        $dispatcher = new Dispatcher($this->getRouter());
        $dispatcher->execute();
        $this->controller = $dispatcher->controller;
    }

    /**
     * 获取请求对象
     * @return mixed
     */
    public function getRequest()
    {
        return Request::getInstance();
    }

    /**
     * 获取路由对象
     * @return mixed
     */
    public function getRouter()
    {
        return Router::getInstance();
    }

    /**
     * 获取session 实例
     * @return \framework\session\Session;
     */
    public function getSession()
    {
        return Session::getInstance();
    }

    /**
     * 获取缓存对象
     * @return \framework\cache\Cache;
     */
    public function getCache()
    {
        return Cache::getInstance();
    }
    /**
     * 自定义异常处理
     * @param \Exception $e exception
     */
    public function appException(\Exception $e)
    {
        $error = array(
            'code'=>    $e->getCode(),
            'message'=> $e->getMessage(),
            'file'=>    $e->getFile(),
            'line'=>    $e->getLine(),
            'trace'=>   $e->__toString());
        Common::halt($error);
    }

    /**
     * 自定义错误处理
     * @param int $errNo
     * @param string $errStr
     * @param string $errFile
     * @param int $errLine
     * @param string $errconText 是一个指向错误发生时活动符号表的 array
     * @throws \Exception
     */
    public function appError($errNo, $errStr, $errFile, $errLine, $errconText='')
    {
        throw new \ErrorException($errStr, 0, $errNo, $errFile, $errLine);
    }
}