<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\controller;

use app\common\model\Addon as AddonModel;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Env;
use think\facade\Hook;
use think\Loader;

/**
 * 插件基类控制器
 * @Author: rainfer <rainfer520@qq.com>
 */
class Base extends Common
{
    // 当前插件操作
    protected $addon      = null;
    protected $controller = null;
    protected $action     = null;
    // 当前template
    protected $template;
    protected $addons_path = '';
    protected $config_file = '';
    // 模板配置信息
    protected $config = [
        'type'         => 'Think',
        'view_path'    => '',
        'view_suffix'  => 'html',
        'strip_space'  => true,
        'view_depr'    => DIRECTORY_SEPARATOR,
        'tpl_begin'    => '{',
        'tpl_end'      => '}',
        'taglib_begin' => '{',
        'taglib_end'   => '}',
    ];

    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        // 初始化配置信息
        $this->config = Config::get('template.') ?: $this->config;
        // 处理路由参数
        $route = request()->path();
        $route = str_ireplace('addons/execute/', '', $route);
        $param = explode('-', $route);
        // 是否自动转换控制器和操作名(是否转换为小写)
        $convert = Config::get('url_convert');
        // 格式化路由的插件位置
        $this->action = $convert ? strtolower(array_pop($param)) : array_pop($param);
        if (stripos($this->action, '/') !== false) {
            $this->action = str_left($this->action, '/');
        }
        $this->controller = $convert ? strtolower(array_pop($param)) : array_pop($param);
        $this->addon      = $convert ? strtolower(array_pop($param)) : array_pop($param);
        // 获取当前插件目录
        $this->addons_path = Config::get('addon_path') . $this->addon . DIRECTORY_SEPARATOR;
        // 读取当前插件配置信息
        if (is_file($this->addons_path . 'config.php')) {
            $this->config_file = $this->addons_path . 'config.php';
        }
        //定义插件路径
        !defined('ADDON_PATH') && define('ADDON_PATH', Env::get('root_path') . 'addons' . DIRECTORY_SEPARATOR);

        // 设置模板路径
        Config::set('template.view_path', $this->addons_path . 'view' . DIRECTORY_SEPARATOR);

        parent::__construct();
    }

    /**
     * 加载模板输出
     * @access protected
     *
     * @param string $template 模板文件名
     * @param array  $vars     模板输出变量
     * @param array  $config   模板参数
     *
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $config = [])
    {
        $controller = Loader::parseName($this->controller);
        if ('think' == strtolower($this->config['type']) && $controller && 0 !== strpos($template, '/')) {
            $depr     = $this->config['view_depr'];
            $template = str_replace(['/', ':'], $depr, $template);
            if ('' == $template) {
                // 如果模板文件名为空 按照默认规则定位
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $this->action;
            } elseif (false === strpos($template, $depr)) {
                $template = str_replace('.', DIRECTORY_SEPARATOR, $controller) . $depr . $template;
            }
        }
        return parent::fetch($template, $vars, $config);
    }

    /**
     * @return mixed
     * @throws
     */
    public function execute()
    {
        if ($this->addon && $this->controller && $this->action) {
            // 获取类的命名空间
            $class = get_addon_class($this->addon, 'controller', $this->controller);
            if (class_exists($class)) {
                $obj = new $class();
                if ($obj === false) {
                    abort(500, '插件初始化失败');
                }
                // 监听addons_init
                Hook::listen('addons_init', $this);
                return call_user_func_array([$obj, $this->action], [$this->request]);
            } else {
                abort(500, '插件控制器不存在');
            }
        }
        abort(500, '插件不存在');
    }

    /**
     * 获取插件的配置的值
     *
     * @param string $name 插件名
     *
     * @return array|mixed|null
     */
    protected function getConfig($name = '')
    {
        static $_config = [];
        if (isset($_config[$name])) {
            return $_config[$name];
        }
        $config = Cache::get('addon_config_' . $name);
        if (!$config) {
            $addon_model = new AddonModel();
            $config      = $addon_model->where('name', $name)->value('config');
            if ($config) {
                $config = json_decode($config, true);
                Cache::set('addon_config_' . $name, $config);
            }
        }
        $_config[$name] = $config;
        return $config;
    }
}
