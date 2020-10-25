<?php

/**
 * Scabish资源调度
 *
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @since 2016-12-2
 */
class SCS {
    
    const VERSION = 2;
    
    private $_sysPath = '';
    
    private $_configs = [
        'debug' => false, // 调试模式开关
        'mode' => 'web', // 运行模式(web|cmd)
        'rewrite' => true, // url重写开关
        'app' => '', // 应用基本目录
        'timezone' => 'PRC', // 系统运行时区
        'namespace' => [  // 命名空间映射
            'Vendor' => 'app://Vendor',
            'Control' => 'app://Control'
        ],
        'db' => [ // 数据库连接信息
            'default' => [ // 默认连接，如果有多个连接，请在default配置后增加
                'dsn' => 'mysql:dbname=db;host=127.0.0.1;charset=utf8',
                'username' => 'root',
                'password' => 'root',
                'prefix' => 'FS' // 表前缀
            ],
        ],
        'log' =>  'index/log', // 错误日志接收处理方法，设置为false则表示关闭日志收集工作
        'lost' => 'index/lost', // 404转接处理方法
        'error' => 'index/error', // 系统运行错误转接处理方法
        'version' => '1.00', // 应用版本号
    ];
    
    private static $_instance;
    
    private function __construct() {}
    
    public function __destruct() {}
    
    public function __clone() {}
    
    /**
     * SCS实例
     * @example
     * SCS::Instance()->version;
     * @return SCS
     */
    public static function Instance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self();
            self::$_instance->_sysPath = dirname(__FILE__);
        }
        return self::$_instance;
    }
    
    /**
     * 读取配置信息
     * @example Scabish::Instance()->mode;
     * @param string $item 配置项
     * @return null|mixed
     */
    public function __get($item) {
        return isset($this->_configs[$item]) ? $this->_configs[$item] : null;
    }
    
    /**
     * 运行框架
     * @param $config 配置信息或配置文件路径
     * @throws Exception
     */
    public function Run($config) {
        $this->SetConfig($config);
        $this->Init();
        
        $rc = new ReflectionClass('Control\\'.$this->Request()->control);
        $action = $this->Request()->action;
        if(!$rc->hasMethod($action)) {
            if(!$rc->hasMethod('__empty')) {
                throw new Exception('Method doest not exist. Current route: '.$this->Request()->route);
            } else {
                $action = '__empty';
            }
        }
        
        $method = $rc->getMethod($action);
        $params = self::checkMethod($method);
        $method->invokeArgs($rc->newInstance(), $params);
    }
    
    /**
     * url管理组件
     * @param boolean cached 是否缓存对象
     * @return SCUrl
     */
    public static function Url($cached = true) {
        static $instance = null;
        if(is_null($instance)) $instance = new \Scabish\Core\Url;
        return $cached ? $instance : new \Scabish\Core\Url;
    }
    
    /**
     * 请求管理组件
     * @return \Scabish\Core\Request
     */
    public static function Request() {
        static $instance = null;
        if(is_null($instance)) $instance = \Scabish\Core\Request::Instance();
        return $instance;
    }
    
    /**
     * 表模型CURD组件
     * @param string $table 表名(无前缀)
     * @param string $connect 数据库连接标识，默认default
     * @return \Scabish\Core\Curd
     */
    public static function Curd($table, $connect = 'default') {
        return new \Scabish\Core\Curd($table, $connect);
    }
    
    /**
     * 数据库操作组件
     * @param string $connect 连接标识，默认default
     * @return \Scabish\Core\Db
     */
    public static function Db($connect = 'default') {
        return new \Scabish\Core\Db($connect);
    }
    
    /**
     * 视图组件
     * @return \Scabish\Core\View
     */
    public static function View() {
        return new \Scabish\Core\View;
    }
    
    /**
     * 分页组件
     * @param integer $page 当前页码
     * @return \Scabish\Core\Page
     */
    public static function Page($page = 0) {
        static $instance = null;
        if(is_null($instance)) $instance = \Scabish\Core\Page::Instance($page);
        return $instance;
    }
    
    /**
     * 设置
     * @param string|array $configs 配置文件所在路径|配置信息
     */
    private function SetConfig($configs) {
        if(!is_array($configs) && file_exists($configs) && is_file($configs)) {
            $configs = include($configs);
        }
        $this->_configs = array_merge($this->_configs, $configs);
        foreach($this->_configs['namespace'] as $namespace=>$path) {
            if(!is_array($path)) $path = [$path];
            array_walk($path, function(&$v) { 
                $v = preg_replace(['/^app\:\/\//', '/^sys\:\/\//'], [$this->app.'/', $this->_sysPath.'/'], $v);
            });
            $this->_configs['namespace'][$namespace] = $path;
        }
        $this->_configs['namespace']['Scabish'] = $this->_sysPath;
    }
    
    /**
     * 构建框架运行环境
     */
    private function Init() {
        version_compare(PHP_VERSION, '5.4.0', 'lt') && die('Scabish requires PHP version 5.4 or higher. (The running PHP version is '.PHP_VERSION.')');
        
        date_default_timezone_set($this->timezone); // 设置系统运行环境时区
        
        // 加载自动加载类
        require $this->_sysPath.'/Core/Autoloader.php';
        // 注册类自动加载机制
        spl_autoload_register(['\Scabish\Core\Autoloader', 'LoadClass']);
        
        /**
         * 错误异常捕捉处理
         * 屏蔽所有页面报错信息，信息被透明捕获到异常处理类进行友好处理
        */
        error_reporting(0);
        ini_set('display_errors', 'Off');
        
        set_error_handler('\Scabish\Core\Error::ErrorHandler', E_ALL);
        set_exception_handler('\Scabish\Core\Error::ExceptionHandler');
        register_shutdown_function('\Scabish\Core\Error::FatalHandler');
        
    }
    
    /**
     * 检查方法所需参数
     * @param object $method
     * @return array 参数结果集
     */
    private function CheckMethod($method) {
        $params = $method->getParameters();
        if(empty($params)) return [];
        $values = [];
        foreach($params as $param) {
            if($param->isDefaultValueAvailable()) {
                if(false === $this->Request()->Param($param->getName())) {
                    $values[] = $param->getDefaultValue();
                } else {
                    $values[] = $this->Request()->Param($param->getName());
                }
            } else {
                if(!array_key_exists($param->getName(), $this->Request()->Param())) {
                    throw new Exception('Param '.$param->getName().' is required in route: '.$this->Request()->route);
                }
                $values[] = $this->Request()->Param($param->getName());
            }
        }
        return $values;
    }
}