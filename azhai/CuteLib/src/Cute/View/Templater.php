<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\View;

use \Cute\View\Compiler;


/**
 * HTML混合模板
 */
class Templater
{
    public $compiler = null;
    public $globals = [];
    protected $source_dir = '';
    protected $extend_files = [];
    protected $template_blocks = [];
    protected $current_block = '';

    /**
     * 构造函数，设置缓存和默认模板目录
     * @param object /null $cache 模板缓存
     */
    public function __construct($source_dir, $compiled_dir = false)
    {
        $this->setSourceDir($source_dir);
        if ($compiled_dir) {
            $this->setCompileDir($compiled_dir);
        }
    }

    /**
     * 设置模板目录
     * @param string $source_dir 模板目录
     */
    public function setSourceDir($source_dir)
    {
        $this->source_dir = rtrim($source_dir, ' /\\');
        if (!file_exists($this->source_dir)) {
            @mkdir($this->source_dir, 0777, true);
        }
    }

    /**
     * 设置模板编译器
     * @param object $compiler 模板编译器
     * @param string $compiled_dir 编译输出目录
     * @return $this
     */
    public function setCompileDir($compiled_dir)
    {
        if (is_null($this->compiler)) {
            $this->compiler = new Compiler();
        }
        $compiled_dir = rtrim($compiled_dir, ' /\\');
        $this->compiler->setSourceDir($this->source_dir);
        $this->compiler->setCompiledDir($compiled_dir);
        $this->setSourceDir($compiled_dir);
        return $this;
    }

    /**
     * 更新全局变量，全局变量可作为编译器变量
     * @param array $globals 全局变量数组
     * @param array ... 其他全局变量数组
     */
    public function updateGlobals(array $globals)
    {
        if (func_num_args() === 1) {
            $this->globals = array_merge($this->globals, $globals);
        } else {
            $args = func_get_args();
            array_unshift($args, $this->globals);
            $this->globals = exec_function_array('array_merge', $args);
        }
        return $this;
    }

    /**
     * 更新编译器函数
     * @param array $functions 函数数组
     */
    public function updateFunctions(array $functions)
    {
        $cpl = $this->compiler;
        foreach ($functions as $name => $func) {
            $cpl->addFunction($name, $func);
        }
    }

    /**
     * 输出内容
     * @param string $template_file 模板文件，相对路径
     * @param array $context 模板变量数组
     */
    public function render($template_file, array $context = [])
    {
        extract($this->globals);
        extract($context);
        ob_start();
        include($this->prepareFile($template_file)); // 入口模板
        if (!empty($this->extend_files)) {
            $layout_file = array_pop($this->extend_files);
            foreach ($this->extend_files as $file) { // 中间继承模板
                include($this->prepareFile($file));
            }
            extract($this->template_blocks);
            include($this->prepareFile($layout_file)); // 布局模板
        }
        $whole_html = trim(ob_get_clean());
        return $whole_html;
    }

    /**
     * 获得模板文件绝对路径，也可能是被编译之后的输出文件
     * @param string $template_file 模板文件，相对路径
     * @return string 模板文件，绝对路径
     */
    public function prepareFile($template_file)
    {
        if ($this->compiler) {
            return $this->compiler->compileTpl($template_file, '', $this->globals);
        } else {
            return $this->source_dir . '/' . $template_file;
        }
    }

    /**
     * 标示上级模板，需要全部标示在开头，无法象Twig一样继承
     * @param type $template_file
     */
    public function extendTpl($template_file)
    {
        array_push($this->extend_files, $template_file);
    }

    /**
     * 包含其他文件的内容
     * NOTE:
     * 必须自己传递context，如果想共享render中的context，请在模板中
     * 使用 include $this->getTemplateFile($template_file); 代替 $this->includeTpl($template_file);
     *
     * @param string $template_file 被包含文件，相对路径
     * @param array $context 局部变量数组
     */
    public function includeTpl($template_file, array $context = [])
    {
        extract($this->globals);
        extract($context);
        ob_start();
        include($this->prepareFile($template_file));
        $include_html = trim(ob_get_clean());
        echo $include_html;
    }

    /**
     * 标示区块开始
     * @param string $block_name 区块名称
     */
    public function blockStart($block_name = 'content')
    {
        $this->current_block = $block_name;
        ob_start();
    }

    /**
     * 标示区块结束
     */
    public function blockEnd()
    {
        $block_html = trim(ob_get_clean());
        $this->template_blocks[$this->current_block] = $block_html;
    }
}
