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
 * 控制器基类
 * @package     core
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
abstract class Controller
{

    /**
     * 模板视图对象
     * @var view
     * @access private
     */
    protected $view = null;
    /**
     * 事件参数
     * @var array
     */
    protected $options = array();

    /**
     * 构造函数
     */
    public function __construct()
    {
        Hook::listen('CONTROLLER_START', $this->options);
        /**
         * 视图对象
         */
        $this->view = ViewFactory::factory();
        /**
         * 自动运行的魔术方法
         */
        if (method_exists($this, "__init")) {
            $this->__init();
        }
    }

    /**
     * 执行不存在的函数时会自动执行的魔术方法
     * 编辑器上传时执行php脚本及ispost或_post等都会执行这个方法
     * @param $action 方法名
     * @param $args 方法参数
     */
    public function __call($action, $args)
    {
        /**
         * 控制器方法不存在时
         */
        if (strcasecmp($action, ACTION) == 0) {
            if (method_exists($this, "__empty")) {
                //执行空方法_empty
                $this->__empty($args);
            } else {
                /**
                 * 404错误页
                 */
                _404('控制器中不存在动作' . $action);
            }
        }
    }

    /**
     * 魔术方法
     * @param $name 变量名
     * @param $value 变量值
     */
    public function __set($name, $value)
    {
        $this->assign($name, $value);
    }

    /**
     * 显示视图
     * @param null $tplFile 模板文件
     * @param int $cacheTime 缓存时间
     * @param null $cachePath 缓存目录
     * @param string $contentType 文件类型
     * @param bool $show 是否显示
     * @return mixed
     */
    protected function display($tplFile = null, $cacheTime = -1, $cachePath = null, $contentType = "text/html", $show = true)
    {
        Hook::listen("VIEW_START");
        //执行视图对象中的display同名方法
        $status = $this->view->display($tplFile, $cacheTime, $cachePath, $contentType, $show);
        Hook::listen("VIEW_END");
        return $status;
    }

    /**
     * 获得视图显示内容 用于生成静态或生成缓存文件
     * @param string $tplFile 模板文件
     * @param null $cacheTime 缓存时间
     * @param string $cachePath 缓存目录
     * @param string $contentType 文件类型
     * @param string $charset 字符集
     * @param bool $show 是否显示
     * @return mixed
     */
    protected function fetch($tplFile = null, $cacheTime = null, $cachePath = null, $contentType = "text/html")
    {
        return $this->view->fetch($tplFile, $cacheTime, $cachePath, $contentType);
    }

    /**
     * 模板缓存是否过期
     * @param string $cachePath 缓存目录
     * @access protected
     * @return mixed
     */
    protected function isCache($cachePath = null)
    {
        $args = func_get_args();
        return call_user_func_array(array($this->view, "isCache"), $args);
    }

    /**
     * 分配变量
     * @access protected
     * @param mixed $name 变量名
     * @param mixed $value 变量值
     * @return mixed
     */
    protected function assign($name, $value = null)
    {
        return $this->view->assign($name, $value);
    }

    /**
     * 错误页面
     * @param string $message 提示内容
     * @param null $url 跳转URL
     * @param int $time 跳转时间
     * @param null $tpl 模板文件
     */
    protected function error($message = '出错了', $url = NULL, $time = 2, $tpl = null)
    {
        if (IS_AJAX) {
            $this->ajax(array('status' => 0, 'message' => $message));
        } else {
            $url = $url ? "window.location.href='" . U($url) . "'" : "window.location.href='" . __HISTORY__ . "'";
            $tpl = $tpl ? $tpl : (strstr(C("TPL_ERROR"), '/') ? C("TPL_ERROR") : MODULE_PUBLIC_PATH . C("TPL_ERROR"));
            $this->assign(array("message" => $message, 'url' => $url, 'time' => $time));
            $this->display($tpl);
        }
        exit;
    }

    /**
     * 成功页面
     * @param string $message 提示内容
     * @param null $url 跳转URL
     * @param int $time 跳转时间
     * @param null $tpl 模板文件
     */
    protected function success($message = '操作成功', $url = NULL, $time = 2, $tpl = null)
    {
        if (IS_AJAX) {
            $this->ajax(array('status' => 1, 'message' => $message));
        } else {
            $url = $url ? "window.location.href='" . U($url) . "'" : "window.location.href='" . __HISTORY__ . "'";
            $tpl = $tpl ? $tpl : (strstr(C("TPL_SUCCESS"), '/') ? C("TPL_SUCCESS") : MODULE_PUBLIC_PATH . C("TPL_SUCCESS"));
            $this->assign(array("message" => $message, 'url' => $url, 'time' => $time));
            $this->display($tpl);
        }
        exit;
    }

    /**
     * Ajax输出
     * @param $data 数据
     * @param string $type 数据类型 text html xml json
     */
    protected function ajax($data, $type = "JSON")
    {
        $type = strtoupper($type);
        switch ($type) {
            case "HTML" :
            case "TEXT" :
                $_data = $data;
                break;
            case "XML" :
                //XML处理
                $_data = Xml::create($data, "root", "UTF-8");
                break;
            default :
                //JSON处理
                $_data = json_encode($data);
        }
        echo $_data;
        exit;
    }

    /**
     * 生成静态
     * @param string $htmlFile 文件名
     * @param string $htmlPath 目录
     * @param string $template 模板文件
     */
    public function createHtml($htmlFile, $htmlPath, $template)
    {
        $content = $this->fetch($template);
        $file = $htmlPath . $htmlFile;
        $Storage = Storage::init();
        return $Storage->save($file, $content);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        Hook::listen('CONTROLLER_END', $this->options);
    }
}