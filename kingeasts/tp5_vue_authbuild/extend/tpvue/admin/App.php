<?php
// +----------------------------------------------------------------------
// | TpAndVue.
// +----------------------------------------------------------------------
// | FileName: App.php
// +----------------------------------------------------------------------
// | Author: King east <1207877378@qq.com>
// +----------------------------------------------------------------------


namespace tpvue\admin;


use think\facade\Config;
use think\facade\Env;
use think\facade\Route;
use think\template\driver\File;
use think\View;
use tpvue\admin\controller\IndexController;
use tpvue\admin\controller\MemberController;
use tpvue\admin\http\middleware\AuthMiddleware;
use tpvue\admin\http\middleware\MemberLoginMiddleware;

class App
{
    static $ROOT_PATH;

    /**
     * @var View $view
     */
    static $view;

    static $config = [];

    public static function bootstrap()
    {
        static::$ROOT_PATH = __DIR__ . '/';

        static::initConfig();


        // +---------------------------------------------------
        // + 加载函数
        // +---------------------------------------------------
        require(static::$ROOT_PATH . 'helper.php');

        // +---------------------------------------------------
        // + 初始化视图
        // +---------------------------------------------------

        $app_path = empty(Env::get('APP_PATH')) ? Env::get('APP_PATH') : '/';
        static::$view = new View();
        static::$view->init([
            'view_path'          => static::$ROOT_PATH . 'view/', // 模板路径
            'view_base'          => '',
            'auto_rule'          => 3,
            'view_suffix'        => 'html', // 默认模板文件后缀
            'view_depr'          => DIRECTORY_SEPARATOR,
            'cache_suffix'       => 'php', // 默认模板缓存后缀
            'tpl_deny_func_list' => 'echo,exit', // 模板引擎禁用函数
            'tpl_deny_php'       => false, // 默认模板引擎是否禁用PHP原生代码
            'tpl_begin'          => '{', // 模板引擎普通标签开始标记
            'tpl_end'            => '}', // 模板引擎普通标签结束标记
            'strip_space'        => false, // 是否去除模板文件里面的html空格与换行
            'tpl_cache'          => false, // 是否开启模板编译缓存,设为false则每次都会重新编译
            'compile_type'       => 'file', // 模板编译类型
            'cache_prefix'       => '__ke__', // 模板缓存前缀标识，可以动态改变
            'cache_time'         => 0, // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
            'layout_on'          => false, // 布局模板开关
            'layout_name'        => 'layout', // 布局模板入口文件
            'layout_item'        => '{__CONTENT__}', // 布局模板的内容替换标识
            'taglib_begin'       => '{', // 标签库标签开始标记
            'taglib_end'         => '}', // 标签库标签结束标记
            'taglib_load'        => true, // 是否使用内置标签库之外的其它标签库，默认自动检测
            'taglib_build_in'    => 'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
            'taglib_pre_load'    => '', // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
            'display_cache'      => false, // 模板渲染缓存
            'cache_id'           => '', // 模板缓存ID
            'tpl_replace_string' => [
                '__ROOT__'     => $app_path,
                '__BASE__'     => $app_path . 'static/base',
                '__PUBLIC__'   => $app_path,
                '__STATIC__'   => $app_path . 'static',
                '__LIBS__'     => $app_path . 'static/libs',
                '__ADMINCSS__' => $app_path . 'static/admin/css',
                '__ADMINJS__'  => $app_path . 'static/admin/js',
                '__ADMINIMG__' => $app_path . 'static/admin/images',
                '__INDEXCSS__' => $app_path . 'static/index/css',
                '__INDEXJS__'  => $app_path . 'static/index/js',
                '__INDEXIMG__' => $app_path . 'static/index/images',
            ],
            'tpl_var_identify'   => 'array', // .语法变量识别，array|object|'', 为空时自动识别
            'default_filter'     => 'htmlentities', // 默认过滤方法 用于普通标签输出
        ]);

        // +---------------------------------------------------
        // + 初始化路由
        // +---------------------------------------------------

        static::initRoute();
    }


    /**
     * 初始化配置
     */
    private static function initConfig()
    {
        $files = glob(static::$ROOT_PATH . 'config/*.php');
        foreach ($files as $file) {
            $name = preg_replace('/\.php$/', '', basename($file));
            static::$config[$name] = require($file);
        }
        unset($files);
    }


    /**
     * 初始化路由
     */
    private static function initRoute()
    {
        $files = glob(static::$ROOT_PATH . 'route/*.php');
        foreach ($files as $file) {
            require($file);
        }
        unset($files);

    }


    /**
     * 获取节点配置,支持使用.获取多级
     * @param string $node
     * @return array|mixed|null
     */
    public static function get($node = null)
    {
        if (is_null($node)) {
            return static::$config;
        } elseif(strpos($node, '.') === false) {
            return static::$config[$node];
        } else {
            $tmp = explode('.', $node);
            $data = static::$config;
            foreach ($tmp as $v) {
                if (isset($data[$v])) {
                    $data = $data[$v];
                } else {
                    return null;
                }
            }
            return $data;
        }
    }

}
