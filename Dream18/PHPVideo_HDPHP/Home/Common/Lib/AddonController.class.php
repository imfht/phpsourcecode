<?php

/**
 * 插件父级控制器
 * Class AddonController
 * @author 后盾向军 <houdunwangxj@gmail.com>
 */
class AddonController
{
    /**
     * 模板视图对象
     * @var view
     * @access private
     */
    protected $view = null;

    public function __construct()
    {
        /**
         * 视图对象
         */
        $this->view = ViewFactory::factory();
        $this->setAddonConfig();
        if (method_exists($this, '__init')) {
            $this->__init();
        }
    }

    //设置插件配置项
    public function setAddonConfig()
    {
        $classFile = APP_ADDON_PATH . MODULE . '/' . MODULE . 'Addon.class.php';
        if (!is_file($classFile)) {
            $this->error('插件不存在');
        }
        require_cache($classFile);
        $class = MODULE . 'Addon'; //类名
        if (!class_exists($class)) {
            $this->error('插件不存在');
        }
        $obj = new $class;
        $config = $obj->getConfig();
        C('addon', $config);
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
}