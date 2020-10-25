<?php
namespace Kernel;

use Kernel\Config;

class View
{
    // 视图实例
    protected static $instance;
    // 模板变量
    protected $data = [];

    protected $replace=[];
    protected $path='';

    /**
     * 架构函数
     * @access public
     * @param array $engine  模板引擎参数
     * @param array $replace  字符串替换参数
     */
    public function __construct()
    {
        $config = Config::instance();

        $this->replace = $config->get('view_replace');
        if($config->get('view_theme')){
            $view_theme = ltrim($config->get('view_theme'),'/').'/';
        }else{
            $view_theme = '';
        }
        $this->path = TEMPLATE_PATH.$view_theme;
    }

    /**
     * 初始化视图
     * @access public
     * @param array $engine  模板引擎参数
     * @param array $replace  字符串替换参数
     * @return object
     */
    public static function instance($engine = [], $replace = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($engine, $replace);
        }
        return self::$instance;
    }

    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name  变量名
     * @param mixed $value 变量值
     * @return $this
     */
    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 解析和获取模板内容 用于输出
     * @param string    $template 模板文件名或者内容
     * @param array     $vars     模板输出变量
     * @param array     $replace 替换内容
     * @param array     $config     模板参数
     * @param bool      $renderContent     是否渲染内容
     * @return string
     * @throws Exception
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [], $renderContent = false)
    {
        // 模板变量
        $vars = array_merge($this->data, $vars);

        // 页面缓存
        ob_start();
        ob_implicit_flush(0);

        // 渲染输出
        $this->includeTemplate($template, $vars, $config);

        // 获取并清空缓存
        $content = ob_get_clean();

        // 允许用户自定义模板的字符串替换
        $replace = array_merge($this->replace, $replace);
        if (!empty($replace)) {
            $content = strtr($content, $replace);
        }
        return $content;
    }
    protected function includeTemplate($template, $vars)
    {
        $templateFile = $this->path.$template.'.php';
        if(!is_file($templateFile)){
            throw new \Exception("template not exist : ".$templateFile);
        }
        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成为独立变量
            extract($vars, EXTR_OVERWRITE);
        }
        //载入模版缓存文件
        include $templateFile;
    }

    /**
     * 视图内容替换
     * @access public
     * @param string|array  $content 被替换内容（支持批量替换）
     * @param string        $replace    替换内容
     * @return $this
     */
    public function replace($content, $replace = '')
    {
        if (is_array($content)) {
            $this->replace = array_merge($this->replace, $content);
        } else {
            $this->replace[$content] = $replace;
        }
        return $this;
    }

    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array  $vars    模板输出变量
     * @param array  $replace 替换内容
     * @param array  $config     模板参数
     * @return mixed
     */
    public function display($content, $vars = [], $replace = [], $config = [])
    {
        return $this->fetch($content, $vars, $replace, $config, true);
    }
}