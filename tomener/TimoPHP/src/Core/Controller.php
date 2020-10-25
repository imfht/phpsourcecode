<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Core;


use Timo\Config\Config;

class Controller
{
    /**
     * 视图实例
     *
     * @var View
     */
    protected $view = null;

    /**
     * 是否启用视图
     *
     * @var bool
     */
    protected $enableView = true;

    public function __construct()
    {
        $this->loadView();
    }

    /**
     * 重定向
     *
     * @param string $url course/show
     * @param array $params ['type' => 2, 'id' => 100]
     * @param string $query_string 'random=2482984&tag=1'
     */
    public function redirect($url, $params = [], $query_string = '')
    {
        if (!parse_url($url, PHP_URL_SCHEME)) {
            $url = $this->view->link($url, $params, $query_string);
        }
        Response::redirect($url);
    }

    /**
     * 成功跳转
     *
     * @param string $msg
     * @param null $url
     * @param array $params
     * @param string $query_string
     * @param string $data
     * @param int $wait
     * @return array|string
     */
    protected function success($msg = '', $url = null, $params = [], $query_string = '', $data = '', $wait = 1)
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } else {
            $url = $this->view->link($url, $params, $query_string);
        }
        return $this->jump(1, $msg, $url, $data, $wait);
    }

    /**
     * 失败跳转
     *
     * @param string $msg
     * @param null $url
     * @param array $params
     * @param string $query_string
     * @param string $data
     * @param int $wait
     * @return array|string
     */
    protected function error($msg = '', $url = null, $params = [], $query_string = '', $data = '', $wait = 3)
    {
        if (is_null($url)) {
            $url = 'javascript:history.back(-1);';
        } else {
            $url = $this->view->link($url, $params, $query_string);
        }

        return $this->jump(0, $msg, $url, $data, $wait);
    }

    /**
     * 跳转
     *
     * @param $code
     * @param $msg
     * @param $url
     * @param $data
     * @param $wait
     * @return array|string
     */
    protected function jump($code, $msg, $url, $data, $wait)
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'url' => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ('html' == strtolower($type)) {
            $tpl = $code == 1 ? 'success' : 'error';
            $this->view->set('layer_on', false);
            $result = $this->view->render(Config::runtime('jump_' . $tpl . '_tpl'), $result);
        } else {
            Response::type($type);
        }
        return $result;
    }

    /**
     * 分配视图数据
     *
     * @param $name
     * @param $value
     */
    protected function assign($name, $value)
    {
        $this->view->assign($name, $value);
    }

    /**
     * 渲染模版并返回内容
     *
     * @param string $template
     * @param array $vars
     * @return string
     */
    protected function render($template = '', $vars = [])
    {
        return $this->view->render($template, $vars);
    }

    /**
     * 渲染模版并直接输出
     *
     * @param string $template
     * @param array $vars
     * @param int $http_response_status
     * @return array|null|string
     */
    protected function display($template = '', $vars = [], $http_response_status = 200)
    {
        if ($http_response_status != 200) {
            Response::sendResponseCode($http_response_status);
        }
        return Response::send($this->view->render($template, $vars), Response::type(), false, false);
    }

    /**
     * 获取当前的response输出类型
     *
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        $isAjax = Request::isAjax();
        return $isAjax ? 'json' : Response::type();
    }

    /**
     * 加载视图组件
     */
    protected function loadView()
    {
        if ($this->enableView === true) {
            //视图文件
            $view_file = APP_DIR_PATH . APP_NAME . DS . 'view' . DS . App::controller() . 'View.php';

            //视图配置
            $view_config = Config::runtime('view') ?: [];

            if (is_file($view_file)) {
                $className = 'app\\' . APP_NAME . '\\view\\' . App::controller() . 'View';
                $this->view = new $className($view_config);
            } else {
                $this->view = View::instance($view_config);
            }
        }
    }
}
