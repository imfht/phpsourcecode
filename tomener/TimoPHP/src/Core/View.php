<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;
use Timo\Exception\CoreException;

class View extends App
{
    /**
     * 视图实例
     *
     * @var View
     */
    protected static $instance = null;

    /**
     * web根目录URL
     *
     * @var string
     */
    protected static $web_url = '';

    /**
     * 主题目录名
     *
     * @var string
     */
    protected $theme = '';

    /**
     * 模版文件
     *
     * @var string
     */
    protected $template = '';

    /**
     * 模板变量
     *
     * @var array
     */
    protected $data = [];

    /**
     * 默认配置
     *
     * @var array
     */
    protected $config = [
        // 模板主题开关
        'theme_on' => true,
        // 默认主题
        'theme' => 'default',
        // 视图文件路径
        'view_path' => '',
        // 视图文件后缀
        'view_suffix' => '.tpl.php',
        // 视图文件分隔符
        'view_depr' => DS,
        // 布局开关
        'layer_on' => false,
        // 布局文件名
        'layer' => 'default',
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 设置视图配置
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->config[$name] = $value;
    }

    /**
     * 视图实例
     *
     * @param array $config
     * @return null|View
     */
    public static function instance(array $config = [])
    {
        if (is_null(static::$instance)) {
            static::$instance = new self($config);
        }
        return static::$instance;
    }

    /**
     * 赋值模版变量
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function assign($name, $value)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
            return $this;
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 渲染模版
     *
     * @param string $template
     * @param array $vars
     * @return string
     * @throws CoreException
     */
    public function render($template = '', $vars = [])
    {
        $this->template = $this->parseTemplate($template);
        $this->data = array_merge($this->data, $vars);

        if (!is_file($this->template)) {
            throw new CoreException('Template ' . $this->template . ' do not exists.');
        }

        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        //模版
        $layer = $this->parseLayer();
        if ($this->config['layer_on'] && is_file($layer)) {
            include $layer;
        } else {
            $this->_loadContent();
        }

        // 获取并清空缓存
        $content = ob_get_clean();

        return $content;
    }

    /**
     * 载入模版
     */
    protected function _loadContent()
    {
        is_file($this->template) && include $this->template;
    }

    /**
     * 载入模板文件
     *
     * @param $file
     */
    protected function _loadFile($file)
    {
        $theme_dir = $this->getThemeDir();
        $file = APP_DIR_PATH . APP_NAME . DS . 'template' . DS . $theme_dir . $file;
        if (is_file($file)) {
            include $file;
        }
    }

    /**
     * 解析模版路径
     *
     * @param $template
     * @return string
     */
    private function parseTemplate($template)
    {
        if (is_file($template)) {
            return $template;
        }
        $theme_dir = $this->getThemeDir();

        $template = !empty($template) ? $template : App::action();

        return APP_DIR_PATH . APP_NAME . DS . 'template' . DS . $theme_dir
        . App::controller() . DS . $template . $this->config['view_suffix'];
    }

    /**
     * 解析layer路径
     *
     * @return string
     */
    private function parseLayer()
    {
        $theme_dir = $this->getThemeDir();
        return APP_DIR_PATH . APP_NAME . DS . 'template' . DS . $theme_dir
        . $this->config['layer'] . '.layer.php';
    }

    /**
     * 获取主题目录
     *
     * @return string
     */
    private function getThemeDir()
    {
        $this->theme = !$this->config['theme_on'] ? '' : $this->config['theme'];

        return !empty($this->theme) ? $this->theme . DS : '';
    }

    /**
     * 生成URL
     *
     * @param string $url
     * @param array $params
     * @param string $query_string
     * @return string
     */
    public function link($url = '', $params = [], $query_string = '')
    {
        $base_path = static::getWebPath();

        if (empty($url) || $url == '/') {
            return $base_path;
        }

        $controller = 'index';
        $action = '';

        $router_config = Config::runtime('router.rules');
        if (is_array($router_config)) {
            foreach ($router_config as $key => $value) {
                $url = str_replace($value, $key, $url);
            }
        }

        $map = explode('/', $url);
        !empty($map[0]) && $controller = $map[0];
        isset($map[1]) && $action = $map[1];
        $url_conf = Config::runtime('url');
        $sub = '';
        $query = '';
        if ($url_conf['mode'] > 0) {
            $query_string = (!empty($query_string) ? ($url_conf['mode'] == 3 ? '&' : '?') . $query_string : '');
            !empty($params) && $query = '/' . implode('/', array_values($params));
            $query .= $url_conf['ext'] . $query_string;
            $sub = $controller . (!empty($action) ? '/' . $action : '');
        }
        switch ($url_conf['mode']) {
            case 0:
                $sub = 'index.php?c=' . $controller . (!empty($action) ? '&a=' . $action : '');
                !empty($params) && $query = '&' . http_build_query($params);
                break;
            case 1:
                $sub = 'index.php/' . $sub;
                break;
            case 2:
                break;
            case 3:
                $sub = 'index.php?' . 'r=' . $sub;
                break;
        }
        return $base_path . $sub . $query;
    }

    /**
     * 获取静态资源URL
     *
     * @param $url
     * @param string $base_path
     * @return string
     */
    public function res($url, $base_path = '')
    {
        if (!$base_path) {
            $base_path = static::getWebPath() . 'static/';
        }
        return $base_path . $url;
    }

    /**
     * 安全输出
     *
     * @param $key
     * @param null $key2
     * @param string $default_value
     * @return mixed|string
     */
    public function opt($key, $key2 = null, $default_value = '')
    {
        if (!isset($this->data[$key])) {
            return $default_value;
        }

        if (!is_null($key2)) {
            if (!isset($this->data[$key][$key2])) {
                return $default_value;
            }
            return $this->data[$key][$key2];
        }

        return $this->data[$key];
    }

    /**
     * 获取应用web目录URL
     *
     * @return string
     */
    public static function getWebPath()
    {
        if (empty(self::$web_url)) {
            $protocol = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $need_path = isset($_SERVER['DOCUMENT_URI']) ? $_SERVER['DOCUMENT_URI'] : $_SERVER['PHP_SELF'];
            static::$web_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . explode('index.php', $need_path)[0];
        }
        return static::$web_url;
    }

    /**
     * 模版中获取变量值
     *
     * @param $name
     * @return mixed
     * @throws CoreException
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        throw new CoreException('Undefined property ' . $name);
    }
}
