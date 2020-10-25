<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\view;

/**
 * Php
 *
 * 使用原生PHP语言模版
 *
 * @package nb\view
 * @link https://nb.cx
 * @author: collin <collin@nb.cx>
 * @date: 2017/11/29
 */
class Protogenesis {

    // 模板引擎参数
    protected $config = [
        // 视图基础目录（集中式）
        'view_base' => '',
        // 模板起始路径
        'view_path' => '',
        // 模板文件后缀
        'view_suffix' => 'php',
        // 模板文件名分隔符
        'view_depr' => DS,
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
        'auto_rule' => 1,
    ];

    protected $template;
    protected $content;

    public function __construct($config = []) {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 检测是否存在模板文件
     * @access public
     * @param string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template) {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        return is_file($template);
    }

    /**
     * 渲染模板文件
     * @access public
     * @param string $template 模板文件
     * @param array $data 模板变量
     * @return void
     */
    public function fetch($template, $data = []) {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new \Exception('template not exists:' . $template, $template);
        }
        $this->template = $template;

        extract($data, EXTR_OVERWRITE);
        include $this->template;
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string $content 模板内容
     * @param array $data 模板变量
     * @return void
     */
    public function display($content, $data = []) {
        $this->content = $content;

        extract($data, EXTR_OVERWRITE);
        eval('?>' . $this->content);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate($template) {
        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = App::$modulePath . 'view' . DS;
        }

        $request = Request::instance();
        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($module, $template) = explode('@', $template);
        }
        if ($this->config['view_base']) {
            // 基础视图目录
            $module = isset($module) ? $module : $request->module();
            $path = $this->config['view_base'] . ($module ? $module . DS : '');
        }
        else {
            $path = isset($module) ? APP_PATH . $module . DS . 'view' . DS : $this->config['view_path'];
        }

        $depr = $this->config['view_depr'];
        if (0 !== strpos($template, '/')) {
            $template = str_replace(['/', ':'], $depr, $template);
            $controller = Loader::parseName($request->controller());
            if ($controller) {
                if ('' == $template) {
                    // 如果模板文件名为空 按照默认规则定位
                    $template = str_replace('.', DS, $controller) . $depr . (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                }
                elseif (false === strpos($template, $depr)) {
                    $template = str_replace('.', DS, $controller) . $depr . $template;
                }
            }
        }
        else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }
        return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
    }

    /**
     * 配置模板引擎
     * @access private
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return void
     */
    public function config($name, $value = null) {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        }
        elseif (is_null($value)) {
            return isset($this->config[$name]) ? $this->config[$name] : null;
        }
        else {
            $this->config[$name] = $value;
        }
    }

}
