<?php

/**
 * 模板
 *
 * @package Comm
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Comm;
class View implements \Yaf_View_Interface {

    /**
     * 模板目录
     *
     * @var string
     */
    protected $_template_dir = '';

    /**
     * 模板变量
     *
     * @var array
     */
    protected $_vars = array();

    /**
     * 模板模块名称临时存储
     *
     * @var string
     */
    protected $_block_name = '';

    /**
     * 模板加载的模块模板临时存储
     *
     * @var string
     */
    protected $_block_tpl = '';

    /**
     * 区块数据
     *
     * @var array
     */
    protected $_block_content = array();

    /**
     * 要展示的HTML数据
     *
     * @var string
     */
    protected $_display_html = '';


    /**
     * Swoole响应对象
     *
     * @var \Swoole\Http\Response
     */
    protected $_response;

    /**
     * Swoole请求对象
     *
     * @var \Swoole\Http\Request
     */
    protected $_request;

    /**
     * 构造方法
     *
     * @param \Swoole\Http\Response $response      SWOOLE响应对象
     * @param \Swoole\Http\Request  $request       Swoole请求对象
     * @param bool                  $auto_set_path 是否自动设置目录
     *
     * @return void
     */
    public function __construct(\Swoole\Http\Response $response = null, \Swoole\Http\Request $request = null, $auto_set_path = true) {
        $response && $this->_response = $response;
        $request && $this->_request = $request;
        $auto_set_path && $this->setScriptPath(APP_PATH . '/views');
    }

    /**
     * The setScriptPath purpose
     *
     * @param string $template_dir 模板目录的绝对路径，默认的Yaf_Dispatcher会设置此目录为application.directory  . "/views".
     *
     * @return void
     */
    public function setScriptPath($template_dir) {
        $this->_template_dir = $template_dir;
    }

    /**
     * The getScriptPath purpose
     *
     * @return void
     */
    public function getScriptPath() {
        return $this->_template_dir;
    }

    /**
     * 为视图引擎分配一个模板变量
     *
     * @param string $name
     * @param string $value
     *
     * @return bool
     */
    public function assign($name, $value = null) {
        if(!$name) {
            return false;
        }

        if(is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $value;
        }
        return true;
    }

    /**
     * 渲染一个视图模板, 并直接输出给请求端
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @return bool
     * @throws \Exception
     */
    public function display($tpl, $tpl_vars = null) {
        if (!($this->_response instanceof \Swoole\Http\Response)) {
            throw new \Exception(200118);
        }


        $old_value = ini_set('xdebug.cli_color', 0);
        $html = $this->render($tpl, $tpl_vars);
        $this->_response->end($html);
        $old_value === false || ini_set('xdebug.cli_color', $old_value);
        return true;
    }

    /**
     * 渲染一个视图模板
     *
     * @param string $tpl
     * @param array $tpl_vars
     *
     * @return string
     */
    public function render($tpl,  $tpl_vars = null) {
        $tpl_vars && $this->assign($tpl_vars);
        return $this->_process($tpl);
    }

    /**
     * 处理模板
     *
     * @param string $tpl 模板文件路径
     *
     * @return void;
     */
    protected function _process($tpl) {
        $this->_display_html = '';
        \extract($this->_vars);

        \ob_start();
        include "{$this->_template_dir}/{$tpl}";
        $this->_display_html .= \ob_get_contents();
        \ob_end_clean();

        $display_html = $this->_display_html;
        $this->_clear();
        return $display_html;
    }

    /**
     * 预定义一个区块
     *
     * @param string $name    名称
     * @param string $default 如果不存在时输出的默认值
     */
    protected function _blockConfig($name, $default = '') {
        if(isset($this->_block_content[$name])) {
            echo $this->_block_content[$name];
        } elseif($default) {
            echo $default;
        }
    }

    /**
     * 开始填充一个区块
     *
     * @param string $name
     */
    protected function _block($name) {
        $this->_display_html .= \ob_get_contents();
        \ob_clean();

        if($this->_block_name) {
            throw new \Comm\Exception\Program('View block is not end.');
        }
        $this->_block_name = $name;
    }

    /**
     * 结束填充一个区块
     */
    protected function _blockEnd() {
        if ($this->_block_name) {
            $this->_block_content[$this->_block_name] = \ob_get_contents();
            \ob_clean();
            $this->_block_name = null;
        }
    }

    /**
     * 加载一个区块模板
     *
     * @param string $tpl
     * @param array  $vars 变量
     */
    protected function _blockLoad($tpl, array $vars = array()) {
        $vars && \extract($vars);
        include "{$this->_template_dir}/{$tpl}.phtml";

        $this->_display_html .= \ob_get_contents();
        \ob_clean();
    }

    /**
     * 加载一个已存在的模板
     *
     * @param string $tpl  模板
     * @param array  $vars 变量
     */
    protected function _include($tpl, array $vars = array()) {
        $vars && \extract($vars);
        include "{$this->_template_dir}/{$tpl}.phtml";
    }

    /**
     * 清理相关变量
     *
     * @return void
     */
    protected function _clear() {
        $this->_block_content = array();
        $this->_block_name = '';
        $this->_block_tpl = '';
        $this->_display_html = '';
        $this->_vars = array();
    }

    /**
     * 加载CSS
     * @param string $path
     * @param boolean $return
     * @param boolean $absolutely
     * @return \mixed
     */
    static public function css($path, $with_version = true, $return = false, $absolutely = false) {
        $href = $absolutely ? $path : self::path('static/' . $path);

        $query = array();
        \Comm\Misc::isProEnv() || $query['debug'] = 1;
        $with_version && $query['version'] = self::cssVer();
        $query && $href .= '?' . http_build_query($query);

        $result = "<link href=\"{$href}\" type=\"text/css\" rel=\"stylesheet\" />";
        if ($return) {
            return $result;
        } else {
            echo $result;
            return null;
        }
    }

    /**
     * 获取图片路径（一般用户加载默认图片）
     *
     * @param string $path  相对路径
     *
     * @return \string
     */
    static public function img($path, $with_version = true) {
        $url = $path;
        if($with_version) {
            $ver = self::cssVer();
            $url .= (strpos($url, '?') === false ? '?' : '&') . "version={$ver}";
        }
        return $url;
    }

    /**
     * 加载JS
     * @param string $path
     * @param boolean $return
     * @param boolean $absolutely
     * @return \mixed
     */
    static public function js($path, $with_version = true, $return = false, $absolutely = false) {
        $src = $absolutely ? $path : self::path('static/' . $path);

        $query = array();
        \Comm\Misc::isProEnv() || $query['debug'] = 1;
        $with_version && $query['version'] = self::jsVer();
        $query && $src .= '?' . http_build_query($query);

        $result = "<script type=\"text/javascript\" src=\"{$src}\"></script>";

        if ($return) {
            return $result;
        } else {
            echo $result;
            return null;
        }
    }

    /**
     * 获取当前CSS版本号
     * @return \string
     */
    static public function cssVer() {
        static $ver = '';
        if (!$ver) {
            $conf = new \Yaf_Config_Ini(ROOTPATH . '/config/env.ini');
            $ver = $conf->version->css;
        }
        return $ver;
    }

    /**
     * 获取JS版本号
     * @return \string
     */
    static public function jsVer() {
        static $ver = '';
        if (!$ver) {
            $conf = new \Yaf_Config_Ini(ROOTPATH . '/config/env.ini');
            $ver = $conf->version->js;
        }
        return $ver;
    }

    /**
     * 拼相对路径URL
     *
     * @param string $path
     * @return string
     */
    static public function path($path = '') {
        $conf = new \Yaf_Config_Ini(ROOTPATH . '/config/env.ini');

        return $conf->setting->relative . $path;
    }
}
