<?php
/**
 * 控制器基类
 * @package     core
 * @author      lajox <lajox@19www.com>
 */
namespace Took;
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
        //视图对象
        $this->view = View::factory();
        //自动运行的魔术方法
        if (method_exists($this, "__init")) {
            $this->__init();
        }
    }

    /**
     * 构造函数
     */
    protected function __init() {}

    /**
     * 执行不存在的函数时会自动执行的魔术方法
     * 编辑器上传时执行php脚本及ispost或_post等都会执行这个方法
     * @param $action 方法名
     * @param $args 方法参数
     */
    public function __call($action, $args)
    {
        //控制器方法不存在时
        if (strcasecmp($action, ACTION) == 0) {
            if (method_exists($this, "__empty")) {
                //执行空方法_empty
                $this->__empty($args);
            } else {
                halt('控制器中不存在动作' . $action);
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
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板显示变量
     * @return mixed
     */
    public function get($name='') {
        return $this->view->get($name);
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
            $bases = array(
                get_view_file( MODULE_VIEW_PUBLIC_PATH . C("TPL_ERROR") ),
                get_view_file( TOOK_TPL_PATH . C("TPL_ERROR") ),
            );
            $base_tpl = get_exists_file($bases);
            $url = $url ? "window.location.href='" . U($url) . "'" : "window.location.href='" . __REFERER__ . "'";
            $tpl = $tpl ? $tpl : (strstr(C("TPL_ERROR"), '/') ? C("TPL_ERROR") : $base_tpl);
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
            $this->ajax(array('status' => 1, 'message' => $message, 'url' => trim($url)));
        } else {
            $bases = array(
                get_view_file( MODULE_VIEW_PUBLIC_PATH . C("TPL_SUCCESS") ),
                get_view_file( TOOK_TPL_PATH . C("TPL_SUCCESS") ),
            );
            $base_tpl = get_exists_file($bases);
            $url = $url ? "window.location.href='" . U($url) . "'" : "window.location.href='" . __REFERER__ . "'";
            $tpl = $tpl ? $tpl : (strstr(C("TPL_SUCCESS"), '/') ? C("TPL_SUCCESS") : $base_tpl);
            $this->assign(array("message" => $message, 'url' => $url, 'time' => $time));
            $this->display($tpl);
        }
        exit;
    }

    /**
     * Ajax输出
     * @param $data 数据
     * @param string $type 数据类型 text html xml json
     * @param int $json_option 传递给json_encode的option参数
     */
    protected function ajax($data, $type = "JSON", $json_option=0)
    {
        $type = strtoupper($type);
        switch ($type) {
            case "HTML" :
            case "TEXT" :
                $_data = $data;
                break;
            case "XML" :
                //XML处理
                $_data = \Tool\Xml::create($data, "root", "UTF-8");
                break;
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
                $_data = $handler.'('.json_encode($data,$json_option).');';
                break;
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                break;
            default :
                //JSON处理
                header('Content-Type:application/json; charset=utf-8');
                $_data = json_encode($data, $json_option);
        }
        echo $_data;
        exit;
    }

    //获取REQUEST数据
    protected function _request($param = '')
    {
        $data = Q('request.');
        if(!empty($param)) {
            $data = $data[$param];
        }
        $args = func_get_args();
        if(empty($data) && isset($args[2])) {
            $data = $args[2];
        }
        else {
            $data = $this->_getFunc($data, $args[1]);
        }
        return $data;
    }

    //获取GET数据
    protected function _get($param = '')
    {
        $data = Q("get.");
        if(!empty($param)) {
            $data = $data[$param];
        }
        $args = func_get_args();
        if(empty($data) && isset($args[2])) {
            $data = $args[2];
        }
        else {
            $data = $this->_getFunc($data, $args[1]);
        }
        return $data;
    }

    //获取POST数据
    protected function _post($param = '')
    {
        $data = Q("post.");
        if(!empty($param)) {
            $data = $data[$param];
        }
        $args = func_get_args();
        if(empty($data) && isset($args[2])) {
            $data = $args[2];
        }
        else {
            $data = $this->_getFunc($data, $args[1]);
        }
        return $data;
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
     * 对数据应用函数
     */
    private function _getFunc($data, $func='') {
        if(!empty($func)) {
            foreach(explode(',',$func) as $fc) {
                if($f = trim($fc)) {
                    $data = $f($data);
                }
            }
        }
        return $data;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        Hook::listen('CONTROLLER_END', $this->options);
    }
}