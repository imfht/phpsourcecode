<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------

/**
 * HDPHP模板引擎
 *
 * @package     Session
 * @subpackage  Driver
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
final class ViewHd
{

    /**
     * 模板变量
     *
     * @var array
     */
    public $vars = array();
    /**
     * 系统常量如__WEB__$const['WEB'];
     *
     * @var array
     */
    public $const = array();
    /**
     * 模版文件
     *
     * @var null
     */
    public $tplFile = null;
    /**
     * 编译文件
     *
     * @var null
     */
    public $compileFile = null;

    /**
     * 模板显示
     *
     * @param string $tplFile 模板文件
     * @param string $cachePath 缓存目录
     * @param int $cacheTime 缓存时间
     * @param string $contentType 文件类型
     * @param bool $show 是否显示
     *
     * @return bool|string
     */
    public function display(
        $tplFile = null, $cacheTime = -1, $cachePath = null,
        $contentType = "text/html", $show = true
    )
    {
        //缓存文件名
        $cacheName = md5($_SERVER['REQUEST_URI']);
        //缓存时间
        $cacheTime = is_numeric($cacheTime)
            ? $cacheTime
            : intval(
                C("TPL_CACHE_TIME")
            );
        //缓存路径
        $cachePath = $cachePath ? $cachePath : APP_CACHE_PATH;
        //内容
        $content = null;
        if ($cacheTime >= 0) {
            $content = S(
                $cacheName, false, $cacheTime,
                array("dir" => $cachePath, 'zip' => false, "Driver" => "File")
            );
        }
        /**
         * 缓存失效
         */
        if (!$content) {
            /**
             * 全局变量定义
             * 模板使用{$hd.get.xx}方式调用
             */
            $this->vars['hd']['get'] = &$_GET;
            $this->vars['hd']['post'] = &$_POST;
            $this->vars['hd']['request'] = &$_REQUEST;
            $this->vars['hd']['cookie'] = &$_COOKIE;
            $this->vars['hd']['session'] = &$_SESSION;
            $this->vars['hd']['server'] = &$_SERVER;
            $this->vars['hd']['config'] = C();
            $this->vars['hd']['language'] = L();
            $this->vars['hd']['const'] = get_defined_constants();
            /**
             * 获得模板文件
             */
            $this->tplFile = $this->getTemplateFile($tplFile);
            if (!$this->tplFile) {
                return;
            }
            //编译文件
            $this->compileFile
                = APP_COMPILE_PATH . MODULE . '/' . CONTROLLER . '/' . ACTION
                . '_' . substr(md5($this->tplFile), 0, 8) . '.php';
            //记录模板编译文件
            if (DEBUG) {
                Debug::$tpl[] = array(basename($this->tplFile),
                    $this->compileFile);
            }
            //编译文件失效（不存在或过期）
            if ($this->compileInvalid($tplFile)) {
                //执行编译
                $this->compile();
            }
            //加载全局变量
            if (!empty($this->vars)) {
                extract($this->vars);
            }
            ob_start();
            include($this->compileFile);
            $content = ob_get_clean();
            //创建缓存
            if ($cacheTime >= 0) {
                //写入缓存
                S(
                    $cacheName, $content, $cacheTime,
                    array("dir" => $cachePath, 'zip' => false,
                        "Driver" => "File")
                );
            }
        }
        if ($show) {
            $charset = C('TPL_CHARSET') ? C('TPL_CHARSET') : "UTF-8";
            if (!headers_sent()) {
                header("Content-type:" . $contentType . ';charset=' . $charset);
            }
            echo $content;
        } else {
            return $content;
        }
    }

    /**
     * 获得视图内容
     *
     * @param null $tplFile 模板文件
     * @param null $cacheTime 缓存时间
     * @param null $cachePath 缓存路径
     * @param string $contentType 文档类型
     *
     * @return bool|string
     */
    public function fetch(
        $tplFile = null, $cacheTime = null, $cachePath = null,
        $contentType = "text/html"
    )
    {
        return $this->display(
            $tplFile, $cacheTime, $cachePath, $contentType, false
        );
    }

    /**
     * 验证缓存是否过期
     *
     * @param string $cachePath 缓存目录
     *
     * @return bool
     */
    public function isCache($cachePath = null)
    {
        $cachePath = $cachePath ? $cachePath : APP_CACHE_PATH;
        $cacheName = md5($_SERVER['REQUEST_URI']);
        return S(
            $cacheName, false, null,
            array("dir" => $cachePath, "Driver" => "File")
        ) ? true : false;
    }

    /**
     * 获得模版文件
     *
     * @param $file 模板文件
     *
     * @return bool|string
     */
    private function getTemplateFile($file)
    {
        if (is_null($file)) {
            /**
             * 没有传参时使用 动作为为文件名
             */
            $file = CONTROLLER_VIEW_PATH . ACTION;
        }
        if (!is_file($file)) {
            if (!strstr($file, '/')) {
                /**
                 * 没有路径时使用控制器视图目录
                 */
                $file = CONTROLLER_VIEW_PATH . $file;
            }
            /**
             * 添加后缀
             */
            if (!preg_match('/\.[a-z]$/i', $file)) {
                $file .= C('TPL_FIX');
            }
        }
        /**
         * 模板文件检测
         */
        if (is_file($file)) {
            return $file;
        } else {
            DEBUG && halt("模板不存在:$file");
            return false;
        }
    }

    /**
     * 编译是否失效
     *
     * @return bool true 失效
     */
    private function compileInvalid()
    {
        $tplFile = $this->tplFile;
        $compileFile = $this->compileFile;

        return DEBUG || !file_exists($compileFile)
        || (filemtime($tplFile) > filemtime($compileFile));
    }

    /**
     * 编译模板
     */
    public function compile()
    {
        /**
         * 编译是否失效
         */
        if (!$this->compileInvalid()) {
            return;
        }
        $compileObj = new ViewCompile();
        $compileObj->run($this);
    }

    /**
     * 向模板中传入变量
     *
     * @param string|array $var 变量名
     * @param mixed $value 变量值
     *
     * @return bool
     */
    public function assign($var, $value)
    {
        if (is_array($var)) {
            foreach ($var as $k => $v) {
                if (is_string($k)) {
                    $this->vars[$k] = $v;
                }
            }
        } else {
            $this->vars[$var] = $value;
        }
    }
}