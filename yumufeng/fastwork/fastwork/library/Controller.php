<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:05
 */

namespace fastwork;


use fastwork\exception\FileNotFoundException;
use traits\JsonResult;

class Controller
{
    use JsonResult;
    /**
     * @var Fastwork
     */
    protected $app;
    /**
     * @var Request
     */
    protected $request;

    private $response;

    public function __construct(Fastwork $app)
    {

        $this->app = $app;
        $this->request = $this->app->request;
        $this->response = $this->app->response;
        $this->init();
    }

    protected function init()
    {
    }

    /**
     * 跳转
     * @param $url
     * @param int $code
     * @return string
     */
    protected function redirect($url, $code = 302)
    {
        return $this->response->redirect($url, $code);
    }

    /**
     * @param $key
     * @param $val
     * @return Controller
     */
    protected function assign($key, $val = '')
    {
        $arr = [];
        if (is_array($key)) {
            $arr = $key;
        } else {
            $arr[$key] = $val;
        }
        $this->app->view->assign($arr);
        return $this;
    }

    /**
     * 渲染模板
     * @param $file
     * @param array $var
     * @return string
     * @throws \HttpResponseException
     */
    protected function fetch($file = null, $var = [])
    {
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        if (strpos($file, '/') !== false) {
            $param = explode('/', $file, 3);
            if (count($param) == 2) {
                $controller = $param[0];
                $action = $param[1];
            } else {
                $module = $param[0];
                $controller = $param[1];
                $action = $param[2];
            }
        } else {
            if (!is_null($file)) {
                $action = $file;
            }
        }
        $ext = $this->app->env->get('config_ext', '.php');
        $path = $this->app->env->get('app_path') . $module . '/view/' . ucfirst($controller) . '/' . $action . $ext;
        if (!is_file($path)) {
            throw new FileNotFoundException("template not exist: " . $path);
        }
        if (!empty($var)) {
            $this->app->view->assign($var);
        }
        ob_start();
        $this->app->view->fetch($path);
        return ob_get_clean();
    }
}