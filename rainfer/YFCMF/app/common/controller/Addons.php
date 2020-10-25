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
use think\View;

/**
 * 插件基类
 * @Author: rainfer <rainfer520@qq.com>
 */
abstract class Addons
{
    /**
     * 视图实例对象
     * @var view
     * @access protected
     */
    protected $view = null;

    // 当前错误信息
    protected $error;
    public $info        = [];
    public $addons_path = '';
    public $config_file = '';

    /**
     * 构造方法
     * @access public
     */
    public function __construct()
    {
        // 获取当前插件目录
        $this->addons_path = Config::get('addon_path') . $this->getName() . DIRECTORY_SEPARATOR;
        // 读取当前插件配置信息
        if (is_file($this->addons_path . 'config.php')) {
            $this->config_file = $this->addons_path . 'config.php';
        }

        // 初始化视图模型
        $config     = ['view_path' => $this->addons_path . 'view/'];
        $config     = array_merge(Config::get('template.'), $config);
        $view       = new View();
        $this->view = $view->init($config);

        // 控制器初始化
        if (method_exists($this, 'initialize')) {
            $this->initialize();
        }
    }

    /**
     * 获取插件的配置的值
     *
     * @param string $name 插件名
     *
     * @return array|mixed|null
     */
    final public function getConfigValue($name = '')
    {
        static $_config = [];
        if (empty($name)) {
            $name = $this->getName();
        }
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
        if (!$config) {
            //默认值
            if (is_file($this->config_file)) {
                $temp_arr = include $this->config_file;
                $config   = parse_config($temp_arr);
                unset($temp_arr);
            }
        }
        $_config[$name] = $config;
        return $config;
    }

    /**
     * 获取当前模块名
     * @return string
     */
    final public function getName()
    {
        $data = explode('\\', get_class($this));
        return strtolower(array_pop($data));
    }

    /**
     * 检查配置信息是否完整
     * @return bool
     */
    final public function checkInfo()
    {
        $info_check_keys = ['name', 'title', 'description', 'status', 'author', 'version', 'admin'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $this->info)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 加载模板和页面输出 可以返回输出内容
     * @access public
     *
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板输出变量
     * @param array  $config   模板参数
     *
     * @return mixed
     * @throws \Exception
     */
    public function fetch($template = '', $vars = [], $config = [])
    {
        if (!is_file($template)) {
            $template = '/' . $template;
        }
        // 关闭模板布局
        $this->view->engine->layout(false);

        return $this->view->fetch($template, $vars, $config);
    }

    /**
     * 渲染内容输出
     * @access public
     *
     * @param string $content 内容
     * @param array  $vars    模板输出变量
     * @param array  $config  模板参数
     *
     * @return mixed
     * @throws \Exception
     */
    public function display($content, $vars = [], $config = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);

        return $this->view->display($content, $vars, $config);
    }

    /**
     * 渲染内容输出
     * @access public
     *
     * @param string $content 内容
     * @param array  $vars    模板输出变量
     *
     * @return mixed
     * @throws \Exception
     */
    public function show($content, $vars = [])
    {
        // 关闭模板布局
        $this->view->engine->layout(false);

        return $this->view->fetch($content, $vars, [], true);
    }

    /**
     * 模板变量赋值
     * @access protected
     *
     * @param mixed $name  要显示的模板变量
     * @param mixed $value 变量的值
     *
     * @return void
     */
    public function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
    }

    /**
     * 获取当前错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    //必须实现安装
    abstract public function install();

    //必须卸载插件方法
    abstract public function uninstall();
}
