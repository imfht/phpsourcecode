<?php

/**
 * FerOS PHP template engine
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 3.0.0
 */

namespace feros;

/**
 * 模板引擎
 * @author sanliang
 */
class view {

    /**
     * 模板解析左分隔符
     * @param string
     */
    public $left_delimiter = '{';

    /**
     * 模板解析右分隔符
     * @param string
     */
    public $right_delimiter = '}';

    /**
     * 模板提示语言
     * @param string
     */
    public $lang = 'zh-cn';

    /**
     * 是否运行模板内插入PHP代码
     * @param bool
     */
    public $php_off = TRUE;

    /**
     * 自动创建子目录
     * @param bool
     */
    public $use_sub_dirs = TRUE;

    /**
     * 是否压缩模板
     * @param bool
     */
    public $strip_space = FALSE;

    /**
     * Gzip数据压缩传输
     * @param bool
     */
    public $header_gzip = FALSE;

    /**
     * 模板缓存过期时间;为-1，则设置缓存永不过期;0可以让缓存每次都重新生成
     * @param int
     */
    public $cache_lifetime = 0;

    /**
     * 编译目录
     * @param string
     */
    public $compile_dir = '';

    /**
     * 缓存目录
     * @param string
     */
    public $cache_dir = '';

    /**
     * 模板目录
     * @param string
     */
    private $view_dir = array();

    /**
     * 模板风格
     * @param string
     */
    public $style = '';

    /**
     * 模板后缀
     * @param string
     */
    public $suffix = '.html';


    /**
     * 缓存文件后缀
     * @var string
     */
    public $cache_suffix = '.cache.php';

    /**
     * 编译文件后缀
     * @var string
     */
    public $compile_suffix = '.compile.php';
    //模板变量
    public $_vars = array();
    public $__ldel, $__rdel;
    public $_view = '';
    static $_lang = array();

    public function __construct() {
        //初始化目录
        $this->compile_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Runtime' . DIRECTORY_SEPARATOR . 'Compile';
        $this->cache_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Runtime' . DIRECTORY_SEPARATOR . 'Cache';
        if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'View'))
            $this->view_dir[] = __DIR__ . DIRECTORY_SEPARATOR . 'View';
    }

    public function __set($var, $value) {
        $this->assign($var, $value);
    }

    /**
     * 增加模板目录
     * @param string $dir
     * @return \feros\view
     */
    public function add_view_dir($dir) {
        $dir = realpath($dir);
        if ($dir && is_dir($dir))
            $this->view_dir[] = $dir;
        return $this;
    }

    /**
     * 设置编译目录
     * @param string $dir
     * @return \feros\view
     */
    public function set_compile_dir($dir) {
        $dir = realpath($dir);
        if ($dir && is_dir($dir))
            $this->compile_dir = $dir;
        return $this;
    }

    /**
     * 设置缓存目录
     * @param string $dir
     * @return \feros\view
     */
    public function set_cache_dir($dir) {
        $dir = realpath($dir);
        if ($dir && is_dir($dir))
            $this->cache_dir = $dir;
        return $this;
    }

    /**
     * 注入变量
     * @access public
     * @param string|NULL $var 变量名称
     * @param * $value 值
     */
    public function assign($var, $value = NULL) {
        is_array($var) ? ($this->_vars = array_merge($this->_vars, $var)) : $this->_vars[$var] = $value;
        return $this;
    }

    /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @param bool $return_cache 是否输出缓存
     * @return void
     */
    public function display($template = NULL, $cacheid = NULL, $return_cache = FALSE) {
        $this->_view.= $this->fetch($template, $cacheid, $return_cache);
    }

    /**
     *  获取输出页面内容
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param bool $return_cache 是否输出缓存
     * @return string
     */
    public function fetch($template = NULL, $cacheid = NULL, $return_cache = FALSE) {
        $this->get_template_file($template);

        if ($return_cache && $this->is_cached($template, $cacheid))
            return file_get_contents($this->get_cache_file($template, $cacheid));


        if (!is_readable($template))
            return;

        ob_start();
        ob_implicit_flush(0);
        extract($this->_vars, EXTR_OVERWRITE);
        include $this->compile($template, $cacheid, $return_cache);
        $content = ob_get_clean();
        if ($this->header_gzip)
            $this->ob_gzip($content);
        if ($return_cache) {
            $cache = $this->get_cache_file($template, $cacheid);
            if (!$this->is_cached($template, $cacheid)) {
                //保存缓存
                $this->mk_dir(dirname($cache));
                file_put_contents($cache, $content);
            }
        }


        return $content;
    }

    /**
     * 返回语言
     * @param string $key
     * @param array $params
     * @return string
     */
    public function get_lang($key, $params = array()) {
        if (empty(self::$_lang)) {
          $lang = __DIR__ . DIRECTORY_SEPARATOR . 'Lang' . DIRECTORY_SEPARATOR . $this->lang . '.php';
            if (is_file($lang)) {
                self::$_lang = include $lang;
            }
        }
        return isset(self::$_lang[$key]) ? str_replace(array_keys($params), array_values($params), self::$_lang[$key]) : NULL;
    }

    /**
     * 是来为GET方式请求
     * @return boolean
     */
    public function is_get() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'GET');
    }

    /**
     * 是来为HTTPS方式请求
     * @return boolean
     */
    public function is_ssl() {
        return(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) || isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] ) ? FALSE : TRUE;
    }

    /**
     * 是来为POST方式请求
     * @return boolean
     */
    public function is_post() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * 是来为PUT方式请求
     * @return boolean
     */
    public function is_put() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT');
    }

    /**
     * 是来为DELETE方式请求
     * @return boolean
     */
    public function is_delete() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE');
    }

    /**
     * 是来为ajax方式请求
     * @return boolean
     */
    public function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * 是来为flash方式请求
     * @return boolean
     */
    public function is_flash() {
        return isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== TRUE || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== TRUE);
    }

    /**
     * 检测缓存是否存在
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return boolean
     */
    public function is_cached($template = NULL, $cacheid = NULL) {
        static $cache = array();

        $key = md5($template . $cacheid);
        if (isset($cache[$key]))
            return $cache[$key];

        $c = $this->get_cache_file($template, $cacheid);


        if (!is_readable($c) || $this->cache_lifetime === 0)
            return $cache[$key] = FALSE;

        if ($this->cache_lifetime === -1)
            return $cache[$key] = TRUE;
        $fromt = filemtime($c);
        if (($fromt + $this->cache_lifetime) < time())
            return $cache[$key] = FALSE;
        return $cache[$key] = TRUE;
    }

    /**
     * 编译模板
     * @param string $template 指定要调用的模板文件
     * @return void
     */
    public function compile($template, $cacheid = NULL, $return_cache = FALSE) {




        $compile = $this->get_compile_file($template, $cacheid);


        if (is_readable($compile)) {
            $savet = filemtime($template);
            $fromt = filemtime($compile);
            if ($savet <= $fromt) {
                return $compile;
            }
        }
        //处理分隔符
        $this->__ldel = preg_quote($this->left_delimiter);
        $this->__rdel = preg_quote($this->right_delimiter);

        $content = file_get_contents($template);

        //开始编译
        require_once __DIR__ . DIRECTORY_SEPARATOR . 'compile.class.php';
        new compile($this, $content);

        //保存编译
        $this->mk_dir(dirname($compile));
        file_put_contents($compile, $content);


        return $compile;
    }

    /**
     * Gzip数据压缩传输 如果客户端支持
     * @param string $content
     * @return string
     */
    public function ob_gzip(&$content) {
        if (!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) {
            $content = gzencode($content, 9);
            header('Content-Encoding:gzip');
            header('Vary:Accept-Encoding');
            header('Content-Length:' . strlen($content));
        }
        return $content;
    }

    /**
     * 返回缓存文件
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return string
     */
    public function get_cache_file($template, $cacheid = NULL) {
        return rtrim($this->cache_dir, '\\//') . DIRECTORY_SEPARATOR . $this->resolve_file($template, $cacheid) . $this->cache_suffix;
    }

    /**
     * 返回编译文件
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return string
     */
    public function get_compile_file($template, $cacheid = NULL) {
        return rtrim($this->compile_dir, '\\//') . DIRECTORY_SEPARATOR . $this->resolve_file($template, $cacheid) . $this->compile_suffix;
    }

    /**
     * 解释引擎文件
     * @access public
     * @param string $template
     * @param string $cacheid
     * @return string
     */
    public function resolve_file($template, $cacheid = NULL) {
        static $resolve = array();
        $template = md5($template . $cacheid);
        if (isset($resolve[$template]))
            return $resolve[$template];
        if ($this->use_sub_dirs) {
            $dir = '';
            for ($i = 0; $i < 6; $i++)
                $dir .= ($template{$i}) . ($template{ ++$i}) . DIRECTORY_SEPARATOR;
            $template = $dir . md5($template);
        }
        return $resolve[$template] = $template;
    }

    /**
     *  获取模板文件
     * @access public
     * @param string $template  模板
     * @return string
     */
    public function get_template_file(&$template) {

        if (is_readable($template))
            return $template;
        $template = str_replace('\\', DIRECTORY_SEPARATOR, $template);

        foreach ($this->view_dir as $value) {
            $tpl = rtrim($value, '\\//') . DIRECTORY_SEPARATOR . ($this->style ? trim($this->style, '\\//') . DIRECTORY_SEPARATOR : '') . $template . $this->suffix;
            if (is_readable($tpl))
                return $template = $tpl;
        }

        throw new \Exception($this->get_lang('template_not_exist',array('{tlp}'=>$template. $this->suffix)));
    }

    /**
     * 创建目录
     * 
     * @param	string	$path	路径
     * @param	string	$mode	属性
     * @return	string	如果已经存在则返回FALSE，否则为flase
     */
    public function mk_dir($path, $mode = 0777) {
        if (is_dir($path))
            return TRUE;
        $_path = dirname($path);
        if ($_path !== $path)
            $this->mk_dir($_path, $mode);
        return @mkdir($path, $mode);
    }

    public function __destruct() {
        echo $this->_view;
    }

}
