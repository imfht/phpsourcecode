<?php

namespace LuciferP\Router;

use LuciferP\Http\Request;
use LuciferP\Http\Response;


/**
 * 该类的目的是实现客户端自定义uri并映射到解析器
 * Class Router
 * @package LuciferP
 * @author Luficer.p <81434146@qq.com>
 */
class Router
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var
     */
    private $response;
    /**
     * @var null
     */
    private $resloveRequest;

    /**
     * @var array
     */
    private $routers = [];
    /**
     * @var array
     */
    private $isRouter = false;
    /**
     * @var string
     */
    private $controller_namespace = '';

    private $error_page = null;

    /**
     * Router constructor.
     * @param Request $request
     */
    public function __construct(Request $request, Response $response, \LuciferP\Router\ResloveRequest $resloveRequest)
    {
        $this->request = $request;
        $this->response = $response;
        $this->resloveRequest = $resloveRequest;

    }


    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setTestRequestData($name, $value)
    {
        $this->request[$name] = $value;
    }

    /**
     * @throws \Exception
     */
    public function run()
    {
        foreach ($this->routers as $key => $router) {
            $valid = $this->resloveRequest->resloveRequest($this, $key, $router);
            if ($valid) {
                if (!($router instanceof \Closure)) {

                    $this->runController($router, $valid);
                } else {
                    $router($this->request, $this->response);
                }
                $this->isRouter = true;

                break;
            }
        }

        if (!$this->isRouter) {
            $this->getErrorPage();
        }

    }

    /**
     * @param $router
     * @throws \Exception
     */
    private function runController($router, $params = [])
    {
        $router = $this->controller_namespace . $router;
        $params = is_array($params) ? $params : [];
        $sp = preg_split("#@#", $router);
        $controller_name = $sp[0];

        if (!class_exists($controller_name)) {
            throw new \Exception("class not exists.{$controller_name}");
        }
        preg_match("#(\w+)@(\w+)#", $router, $match);
        $controller_id = $match[1];
        $action = $sp[1];
        $controller = new $controller_name($controller_id, $action, $this->request, $this->response);
        $ret = call_user_func_array([$controller, $action], $params);
        $this->response->send($ret);
    }

    public function getErrorPage()
    {
        return $this->error_page ? $this->response->render($this->error_page,[]):
        $this->response->status(404)->send("not found");
    }

    public function setErrorPage($page,$code=404,$message='')
    {
        $this->response->status($code);
        $this->response->setResponseBody($message);
        $this->error_page = $page;
    }

    /**
     * @param $path
     * @param \Closure $closure
     */
    public function get($path, $closure)
    {

        $this->doRouter(__METHOD__, $path, $closure);

    }

    /**
     * @param $method
     * @param $path
     * @param $object
     */
    private function doRouter($method, $path, $object)
    {
        $params = $method . '###' . $path;

        if ($object instanceof \Closure) {
            $this->routers[$params] = $object->bindTo($this, __CLASS__);

        } else {

            $this->routers[$params] = $object;
        }


    }

    /**
     * @param $path
     * @param \Closure $closure
     */
    public function post($path, $closure)
    {
        $this->doRouter(__METHOD__, $path, $closure);

    }

    /**
     * @param $path
     * @param \Closure $closure
     */
    public function all($path, $closure)
    {
        $this->doRouter(__METHOD__, $path, $closure);

    }

    /**
     * @param $path
     * @param \Closure $closure
     */
    public function auth($path, $closure)
    {
        $this->doRouter(__METHOD__, $path, $closure);

    }

    /**
     * @param $path
     */
    public function setControllerNameSpace($path)
    {
        $this->controller_namespace = $path;
    }


}