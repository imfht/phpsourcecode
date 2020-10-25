<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;
define('__VER__'   ,'2.0.4');//版本号
define('__PHASE__' ,'alpha');//阶段
/**
 * 框架配置类
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/3/30
 *
 * @property  boolean debug 默认关闭调试
 * @property  string path
 * @property  string register 注入一个类，来自定义框架里的一些事件，如报错处理
 * @property  string folder_app 默认模块文件夹名字
 * @property  string folder_moudle 模块文件夹名字
 * @property  string folder_controller 默认控制器文件夹名字
 * @property  string folder_console 默认console控制器文件夹名字
 * @property  string composer
 *
 * @property  array dao
 * @property  array cookie
 * @property  array session
 * @property  array swoole
 * @property  array router 路由配置
 *
 * @property array i18n 多语言配置
 */
class Config extends Access {

    /**
     * @var Config
     */
    public static $o;

    /**
     * 配置格式 show:all-server-session-trace,n:20,key:123,ip:127.0.0.1-127.0.0.2
     * ip:127.0.0.1-127.0.0.2 指定只记录来自127.0.0.1和127.0.0.2的信息
     * key:123 设置访问debug的密码为123
     * show:all-server-session-trace 设置debug记录的额外信息,如果有all,则无需填写其它值
     */
    //public $debug               = false;

    protected function _pool() {
        return ['nb\event\Framework'];
    }

    //composer的autoload文件完整路径
    protected function _composer() {
        return __APP__.'vendor/autoload.php';
    }

    //模块文件夹名字
    protected function _folder_module() {
        return 'module';
    }

    //默认控制器文件夹名字
    protected function _folder_controller() {
        return 'controller';
    }

    //默认console控制器文件夹名字
    protected function _folder_console() {
        return 'controller';
    }

    //默认模块文件名
    protected function _folder_app() {
        return 'application';
    }

    //程序根目录
    protected function _path() {
        return __APP__;
    }

    //注入一个类，来自定义框架里的一些事件，如报错处理
    protected function _register() {
        return 'nb\\event\\Framework';
    }

    //命名空间
    //自动替换命名空间第一节点为对应值.以达到简化命名空间长度
    //public $namespace    = [];

    //web以根域名请求时的后戳名
    //public $default_ext         = '';

    //web以根域名请求时的默认首页，后戳名，默认模块
    protected function _default_index() {
        return 'index';
    }

    //默认的控制器方法
    protected function _default_func() {
        return 'index';
    }

    protected function _default_timezone() {
        return 'PRC';
    }

    //缓存目录路径,需要填写绝对路径，用来缓存模版编译文件，数据缓存文件
    protected function _path_temp() {
        return __APP__.'tmp'.DS;
    }

    //日志目录路径,需要填写绝对路径
    protected function _path_log() {
        return __APP__.'tmp'.DS.'log'.DS;
    }

    //自动包含路径
    public function _path_autoinclude() {
        return [__APP__.'application'.DS.'include'.DS];
    }

    //自动包含的文件标示,可为数组和字符串
    //数组为host和对应标示的键值对
    //public $path_autoext;

    //路由配置
    //public $router;

    //注册模块
    //public $module_register = [];

    //模块绑定域名
    //public $module_bind = [];

    //public $server  = 'swoole';

    //运行方式
    //public $sapi;//                = php_sapi_name()=='cli'?'cli':'web';
    protected function _sapi() {
        return php_sapi_name()=='cli'?'command':'php';
    }

    //public $path                =  __APP__;

    //NB框架根目录
    public $path_nb             =  __NB__;

    //数组配置文件路径
    protected $path_arr = __APP__ .'config.inc.php';

    //业务配置存储点
    public $config              = [];

    /**
     * 控制器里不允许对外访问的公共方法
     * @var array
     */
    public $notFunc = [
        '__before',
        '__after',
        '__validate',
        '__error'
    ];


    //多语言配置
    //public $lang                = [];

    //模板路径,默认原生和编译模版共用路径
    //public $path_templates      = __APP__.'application'.DS.'view'.DS;

    //单数据库时可添加此配置,多数据库时,需要使用自定义配置
    /*
    public $db                  = [
        'driver'	=> 'mysql',
        'host' 		=> '127.0.0.1',
        'port' 		=> '3306',
        'dbname'    => 'test',
        'user' 		=> 'root',
        'pass' 		=> '123456',
        'connect'   => 'false',
        'charset' 	=> 'UTF8',
        'prefix'    => '', // 数据库表前缀
    ];
    */

    //文件缓存配置
    //public $cache            = [
    //    'expire'    => 0,
    //    'ext' => '.cache',
    //];

    //Session设置
    /*
    public $session = [
        'driver'=>'',
        'id'             => '',
        'var_session_id' => '',// SESSION_ID的提交变量,解决flash上传跨域
        'prefix'         => 'nb_',// SESSION 前缀
        'type'           => '',// 驱动方式 支持redis memcache memcached
        'auto_start'     => true,// 是否自动开启 SESSION
    ];
    */

    //单redis时可添加此配置,多redis时,需要使用自定义配置
    /*
    public $redis               = [
        'host'    => '127.0.0.1',
        'port'    => 6379,
        'db'      => 0,
        'timeout' => 0,
        'connect' => false,
    ];
    */
    //单Memcache时可添加此配置,多redis时,需要使用自定义配置
    /*
    public $memcache            = [
        'host'    => '127.0.0.1',
        'host'    => 11287,
        'connect' => false,
    ];
    */

    //模版引擎配置
    //public $view = [];

    //public $composer = __APP__.'vendor/autoload.php';

    private function __construct($config=[]) {
        if(is_file($this->path_arr)) {
            $conf = include($this->path_arr);
            is_array($conf) and $config = array_merge($conf,$config);
        }
        $this->tmp = $config;
        defined('DEBUG') and $this->debug = DEBUG;
        if($this->debug) {
            ini_set('display_errors','On');
            error_reporting(E_ALL);
        }
        $this->argv = $_ENV['argv'];

        spl_autoload_register([$this, 'onLoaderHandler'],null,true);
        // 设置系统时区
        date_default_timezone_set($this->default_timezone);
    }

    /**
     * 注册当前配置对象
     *
     * @param null $config
     * @throws \ReflectionException
     */
    public static function register(array $config=[]) {
        $class = get_called_class();

        self::$o = new $class($config);

        Exception::register();

        self::$o->register and Pool::object(
            'nb\event\Framework',
            Config::$o->register
        );

        Pool::object('nb\event\Framework')->config(self::$o);

        self::$o->import(self::$o->path_autoinclude);

        is_file(self::$o->composer) and require self::$o->composer;
    }

    /**
     * 获取扩展配置文件里值
     * @param $name
     * @return null
     */
    public static function get($name=null){
        if(isset(self::$o->config[$name])) {
            return self::$o->config[$name];
        }
        if (strpos($name, '.')) {
            list($field,$index) = explode('.', $name, 2);
            $field = self::$o->config[$field];
            if(isset($field[$index])) {
                return $field[$index];
            }
            return null;
        }
        if($name === null) {
            return self::$o->config;
        }
        return null;
    }

    /**
     * 对扩展配置文件里值进行修改
     * @param $k
     * @param $v
     * @return mixed
     */
    public static function set($k,$v=null) {
        if(is_array($k)) {
            return self::$o->config = array_merge(self::$o->config,$k);
        }
        return self::$o->config[$k]=$v;
    }

    /**
     * 设置框架配置里的值,针对数组,以追加的形式添加
     * @param $name 如果name为数组，直接和$this->config合并
     *              否则，直接和name对应对键合并，
     * @return mixed
     */
    public static function set_merge($name, $value=null){
        if(is_array($name)) {
            self::$o->config = array_merge(self::$o->config,$name);
        }
        else {
            self::$o->config[$name] = array_merge(self::$o->config[$name],$value);
        }
        return true;
    }

    /**
     * 获取框架配置里的值
     * @param $name
     * @return mixed
     */
    public static function getx($name){
        if (strpos($name, '.')) {
            list($field,$index) = explode('.', $name, 2);
            $field = self::$o->$field;
            if(isset($field[$index])) {
                return $field[$index];
            }
            return null;
        }
        return self::$o->$name;
    }

    /**
     * 待废弃
     *
     * 设置框架配置里的值
     * @param $name
     * @return mixed
     */
    public static function setx($name,$value){
        return self::$o->$name = $value;
    }

    /**
     * 设置框架配置里的值,针对数组,以追加的形式添加
     * @param $name
     * @return mixed
     */
    public static function setx_merge($name,$value){
        self::$o->$name = array_merge(self::$o->$name,$value);
        return self::$o->$name;
    }

    function load($file,$ext = 'php'){
        static $isload;
        $file = $file . '.' . $ext;
        if($isload[$file]) {
            return;
        }
        $path = $this->path_autoinclude;// \nb\Config::$o->getx('path_autoinclude');
        foreach($path as $v) {
            if(is_file($v.$file)) {
                include $v.$file;
                if(isset($config) && is_array($config)) {
                    $this->config = array_merge($this->config,$config);
                    //\nb\Config::$o->config = array_merge(\nb\Config::$o->config,$config);
                }
                $isload[$file] = true;
                return;
            }
        }
    }

    /**
     * 将指定文件夹下的文件include
     * @param $load
     */
    public function import(array $path,$exts=null){
        $exts = $exts?:$this->path_autoext;
        $ext = '*.{inc.php,fuc.php';
        if($this->sapi=='cli') {
            $ext .= ',cli.php';
            is_string($exts) and $ext .= ','.$exts.'.php';
        }
        else {
            if(is_string($exts)){
                $ext .= ','.$exts.'.php';
            }
            /*
            $host = Request::driver()->host;
            if($host && is_array($exts) && isset($exts[$host])) {
                $ext .= ','.$exts[$host].'.php';
            }
            else if(is_string($exts)){
                $ext .= ','.$exts.'.php';
            }
            */
        }
        $config = [];
        $ext .='}';
        $auto = [];
        foreach ($path as $val) {
            $tmp = glob($val . $ext,GLOB_BRACE);
            $tmp and $auto = array_merge($auto,$tmp);
        }
        foreach ($auto as $v) {
            $conf = include $v;
            is_array($conf) and $config=array_merge($config,$conf);
        }
        $this->config = array_merge($this->config,$config);
    }

    /**
     * 自动加载
     * @param $object
     */
    protected function onLoaderHandler($object) {
        $ex = explode('\\',$object);
        $count = count($ex);
        switch (true) {
            case isset($this->namespace[$ex[0]]):
                $path = str_replace($ex[0], $this->namespace[$ex[0]], $object).'.php';
                $path = str_replace('\\', '/', $path);
                $first = substr( $path, 0, 1 );
                if($first !== '/') {
                    $path = __APP__.$path;
                }
                break;
            case $ex[0]=='nb':
                $path = str_replace('nb\\', __NB__, $object).'.php';
                $path = str_replace('\\', '/', $path);
                break;
            case $count == 2:
                $path = __APP__.$this->folder_app.'/'.$ex[0].'/'.$ex[1].'.php';
                if(is_file($path) ) {
                    return include $path;
                }
            default:
                $path = __APP__.str_replace('\\', '/', $object).'.php';
                break;
        }
        is_file($path) and include($path);
    }

    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param  string  $name 字符串
     * @param  integer $type 转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true) {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}
