<?php
/*
 * MY_Templater
 * @author Ryan Liu <http://azhai.oschina.io/>
 * @copyright (c) 2013-2017 MIT License
 */


/**
 * 简单够用的PHP模板引擎.
 */
class MY_Templater
{
    public static $mime_types = [
        'htm'  => 'text/html',
        'html' => 'text/html',
        'css'  => 'text/css',
        'txt'  => 'text/plain',
        'xml'  => 'text/xml',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'png'  => 'image/png',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'bin'  => 'application/octet-stream',
    ];
    public $globals = [];
    public $charset = '';
    protected $frame_files = [];
    protected $stack = [];
    protected $blocks = [];
    
    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        if (isset($config['globals']) && is_array($config['globals'])) {
            $this->addGlobal($config['globals']);
        }
    }
    
    /**
     * 添加一个或多个全局变量.
     *
     * @param string|array $key   一个变量名或变量数组
     * @param mixed        $value 变量值
     * @return this
     */
    public function addGlobal($key, $value = null)
    {
        if (is_array($key)) {
            $this->globals = array_replace($this->globals, $key);
        } else {
            $this->globals[$key] = $value;
        }
        return $this;
    }
    
    /**
     * 发送HTTP错误.
     */
    public static function abort($code = 500)
    {
        $code = is_numeric($code) ? intval($code) : 500;
        http_response_code($code);
        if ($code >= 400) {
            return sprintf('<h1>%s</h1>', 'An Error Was Encountered');
        }
    }
    
    /**
     * 页面跳转，GET方式.
     *
     * @param string $to_url    要跳转网址
     * @param bool   $permanent 是否永久跳转(HTTP 301)
     *
     * @return 进入新页面
     */
    public static function redirect($to_url = '', $permanent = false)
    {
        $status_code = $permanent ? 301 : 302;
        self::header('Location', $to_url, true, $status_code);
        return die(); //阻止运行后面的代码
    }
    
    /**
     * 发送Header.
     */
    public static function header($name, $value, $replace = true, $code = 200)
    {
        if (!headers_sent()) {
            $line = empty($name) ? '' : strval($name) . ': ';
            $line .= is_array($value) ? implode(' ', $value) : strval($value);
            @header($line, $replace, $code);
        }
    }
    
    /**
     * 添加模板文件.
     *
     * @param string $frame_file 模板文件
     * @return this
     */
    public function addFrameFile($frame_file)
    {
        if ($frame_file && is_readable($frame_file)) {
            $this->frame_files[] = $frame_file;
        }
        return $this;
    }
    
    /**
     * 设置布局文件.
     *
     * @param string $layout_file 布局文件
     * @return this
     */
    public function extendTpl($layout_file)
    {
        if ($layout_file && is_readable($layout_file)) {
            array_unshift($this->frame_files, $layout_file);
        }
        return $this;
    }
    
    /**
     * 包含模板文件.
     *
     * @param string $frame_file 模板文件
     */
    public function includeTpl($frame_file)
    {
        if ($frame_file && is_readable($frame_file)) {
            extract($this->globals);
            include $frame_file;
        }
    }
    
    /**
     * 标示区块开始.
     *
     * @param string $name 区块名称
     */
    public function blockStart($name = 'content')
    {
        array_push($this->stack, $name);
        ob_start();
    }
    
    /**
     * 标示区块结束
     */
    public function blockEnd()
    {
        $block_html = trim(ob_get_clean());
        if ($name = array_pop($this->stack)) {
            if (isset($this->blocks[$name])) {
                return; //防止底层覆盖上层同名block
            }
            $this->blocks[$name] = $block_html;
        }
    }
    
    /**
     * 返回区块内容.
     *
     * @param string $name 区块名称
     */
    public function block($name = 'content')
    {
        if (isset($this->blocks[$name])) {
            return $this->blocks[$name];
        }
    }
    
    /**
     * 设置文档类型和字符集
     *
     * @param string $type    文档类型
     * @param string $charset 字符集
     * @return this
     */
    public function setContentType($type, $charset = 'utf-8')
    {
        $line = self::$mime_types[$type];
        if ($charset) {
            $this->charset = strval($charset);
            $line .= '; charset=' . $this->charset;
        }
        self::header('Content-Type', $line);
        return $this;
    }
    
    /**
     * 获取输出内容.
     *
     * @param array $context 模板变量数组
     * @return string
     */
    public function render(array $context = [])
    {
        extract($this->globals);
        extract($context);
        ob_start();
        //frame_files数组在动态增长，不能使用for循环
        while (count($this->frame_files)) {
            $frame_file = array_pop($this->frame_files);
            include $frame_file;
        }
        return trim(ob_get_clean());
    }
}
