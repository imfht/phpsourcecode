<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace esclass;


class Controller
{
    protected $view;
    protected $request;

    /**
     * 构造方法
     *
     * @param Request $request Request对象
     * @access public
     */

    public function __construct()
    {

        $this->view = ismarty::instance();

        $this->request = Request::instance();
        // 控制器初始化
        $this->_initialize();


    }

    // 初始化
    protected function _initialize()
    {
    }

    public function send($contentType, $data, $type)
    {


        if ($type == 'json') {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }


        if (!headers_sent()) {
            // 发送状态码
            http_response_code(200);
            header('Content-Type:' . $contentType . '; charset=utf-8');

        }
        exit($data);


        if (function_exists('fastcgi_finish_request')) {
            // 提高页面响应
            fastcgi_finish_request();
        }
    }

    protected function error($msg = '', $url = null, $data = '', $wait = 3)
    {


        $this->showstatus($msg, $url, $data, $type = 'error', $wait);
    }

    protected function success($msg = '', $url = null, $data = '', $wait = 3)
    {

        $this->showstatus($msg, $url, $data, $type = 'success', $wait);
    }

    /**
     * 操作成功或失败的跳转的快捷方法
     *
     * @access protected
     * @param mixed   $msg  提示信息
     * @param string  $url  跳转的URL地址
     * @param mixed   $data 返回的数据
     * @param integer $wait 跳转等待时间
     * @param array   $type 为'success'或'error'
     * @return void
     */
    protected function showstatus($msg = '', $url = null, $data = '', $statustype = 'success', $wait = 3)
    {
        if ($statustype == 'success') {
            $code = 1;
            if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
                $url = $_SERVER["HTTP_REFERER"];
            } elseif ('' !== $url) {
                $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : es_url($url);
            }
        } else {
            $code = 0;
            if (is_null($url)) {
                $url = Request::instance()->isAjax() ? '' : 'javascript:history.back(-1);';
            } elseif ('' !== $url) {
                $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : es_url($url);
            }
        }

        if (is_numeric($msg)) {
            $code = $msg;
            $msg  = $data['msg'];
        }

        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();

        if ('html' == strtolower($type)) {
            $module = Request::instance()->module();
            $config = load_config(APP_PATH . DS . $module . '/config.php');
            $this->assign($result);

            $result      = $this->fetch($config['dispatch_' . $statustype . '_tmpl']);
            $contentType = 'text/html';
        }
        if (strtolower($type) == 'json') {
            $contentType = 'application/json';

        }

        try {
            $response = $this->send($contentType, $result, strtolower($type));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * 获取当前的response 输出类型
     *
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        $isAjax = Request::instance()->isAjax();
        return $isAjax ? config('config.default_ajax_return') : config('config.default_return_type');
    }

    /**
     * URL跳转
     *
     * @param string $url  跳转地址
     * @param int    $time 跳转延时(单位:秒)
     * @param string $msg  提示语
     */
    function redirect($url, $msg = '', $time = 0)
    {

        $url = str_replace(["\n", "\r"], '', $url); // 多行URL地址支持
        dump($url);
        if (empty($msg)) {
            $msg = "系统将在 {$time}秒 之后自动跳转到 {$url} ！";

        }
        if (headers_sent()) {
            $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0) {
                $str .= $msg;
            }
            exit($str);
        } else {
            if (0 === $time) {
                header("Location: " . $url);
            } else {
                header("Content-type: text/html; charset=utf-8");
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        }
    }


    public function assign($k, $v = null)
    {
        $this->view->assign($k, $v);

    }

    public function fetch($template = '')
    {


        return $this->view->display($template);
    }

    public function fetchcontent($template = '')
    {


        return $this->view->returncontent($template);
    }

    /*
     * 获取验证码
     *
     */
    public function getcode($id = '')
    {

        getcode($id);


    }


}