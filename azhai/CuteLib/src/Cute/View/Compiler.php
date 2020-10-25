<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\View;
\app()->import('Twig', VENDOR_ROOT . '/Twig/lib');


/**
 * 模板编译器
 */
class Compiler
{
    protected $env = null;
    protected $source_dir = '';
    protected $compiled_dir = '';

    public function __construct()
    {
        $this->env = new \Twig_Environment();
    }

    /**
     * 为模板添加函数实现
     * @param string $name 函数别名
     * @param callable $function 调用的实现函数
     * @return null
     */
    public function addFunction($name, $function = null)
    {
        $simple_function = new \Twig_SimpleFunction($name, $function);
        return $this->env->addFunction($name, $simple_function);
    }

    /**
     * 设置模板文件根目录
     * @param type $source_dir 模板文件根目录
     * @return $this
     */
    public function setSourceDir($source_dir)
    {
        $this->source_dir = rtrim($source_dir, ' /\\');
        if (!file_exists($this->source_dir)) {
            @mkdir($this->source_dir, 0777, true);
        }
        $loader_fs = new \Twig_Loader_Filesystem([$this->source_dir]);
        $this->env->setLoader($loader_fs);
        return $this;
    }

    /**
     * 设置目标文件根目录
     * @param string $compiled_dir 目标文件根目录
     * @return $this
     */
    public function setCompiledDir($compiled_dir)
    {
        $compiled_dir = rtrim($compiled_dir, ' /\\');
        if ($this->compiled_dir !== $compiled_dir) {
            $this->compiled_dir = $compiled_dir;
        }
        if (!file_exists($this->compiled_dir)) {
            @mkdir($this->compiled_dir, 0777, true);
        }
        return $this;
    }

    /**
     * 将目录下的所有Twig模板文件编译成PHP模板文件
     * @param type $dir 模板目录，相对路径
     * @param array $context 所有模板变量数组
     * @return int 成功编译文件数量
     */
    public function compileAll($dir = false, array $context = [])
    {
        if (is_null($dir) || $dir === false) {
            $dir = $this->source_dir;
        } else {
            $dir = rtrim(str_replace('\\', '/', $dir), '/');
        }
        $count = 0;
        $children = scandir($dir);
        foreach ($children as $child) {
            if ($child === '.' || $child === '..') {
                continue;
            }
            $source = $dir . '/' . $child;
            if (is_dir($source)) {
                $this->compileAll($source);
            } else {
                $template_file = substr($source, strlen($this->source_dir));
                $this->compileTpl($template_file, $source, $context);
                $count++;
            }
        }
        return $count;
    }

    /**
     * 将Twig模板文件编译成PHP模板文件
     * @param string $template_file 模板文件名
     * @param string $source_file 源文件，相对路径
     * @param array $context 变量数组
     * @return string
     */
    public function compileTpl($template_file, $source_file = '', array $context = [])
    {
        $template_file = ltrim(str_replace('\\', '/', $template_file), '/');
        if (empty($source_file)) {
            $source_file = $this->source_dir . '/' . $template_file;
        }
        $compiled_file = $this->compiled_dir . '/' . $template_file;
        $compiled_dir = dirname($compiled_file);
        if (!is_dir($compiled_dir)) {
            mkdir($compiled_dir, 0777, true);
        }
        if ($this->isSourceRelational()) {
            $source_file = substr($source_file, strlen($this->source_dir) + 1);
        }
        $this->compileFile($source_file, $compiled_file, $context);
        return $compiled_file;
    }

    /**
     * 源文件是否采用相对路径
     * @return bool
     */
    public function isSourceRelational()
    {
        return true;
    }

    /**
     * Twig编译文件
     * @param string $source_file 源文件，相对路径
     * @param string $compiled_file 目标文件，绝对路径
     * @param array $context 变量数组
     */
    public function compileFile($source_file, $compiled_file, array $context = [])
    {
        $content = $this->env->loadTemplate($source_file)->render($context);
        file_put_contents($compiled_file, $content, LOCK_EX);
    }
}
