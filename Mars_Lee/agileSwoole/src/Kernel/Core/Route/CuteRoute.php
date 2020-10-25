<?php

namespace Kernel\Core\Route;

use Component\Producer\IProducer;
use Component\Producer\Producer;
use Kernel\Core\Route\Cute\Router;
use Kernel\AgileCore as Core;
use Kernel\Core\Conf\Config;


class CuteRoute implements IRoute
{
    protected $routes = [];
    /* @var $router Router */
    private $router;

    public static function getInstance(): IRoute
    {
        return Core::getInstance()->getContainer()->get('route');
    }

    public function __construct(Config $config)
    {
        $this->routes = $config->get('route');
        $this->router = new Router();
        $this->_init();
    }

    protected function _init()
    {
        $methods = ['get', 'post', 'head', 'options', 'delete', 'put', 'patch'];
        foreach ($this->routes as $method => $routes) {
            if (!in_array($method, $methods)) {
                throw new \Exception('router method error: ' . $method);
            }
            foreach ($routes as $route) {
                if (isset($route['path']) and isset($route['dispatch'])) {
                    $dispatch = [
                        'dispatch' => $route['dispatch']
                    ];

                    if (isset($route['after'])) {
                        $dispatch['after'] = $route['after'];
                    }

                    if (isset($route['before'])) {
                        $dispatch['before'] = $route['before'];
                    }

                    if (isset($route['type'])) {
                        $dispatch['type'] = $route['type'];
                    }
                    $this->add($method, $route['path'], $dispatch);
                }
            }

        }
    }

    public function add(string $method, string $path, $closure): IRoute
    {
        $this->router->$method($path, $closure);
        return $this;
    }


    public function get(string $path, \Closure $closure = null)
    {
        return $this->router->get($path, $closure);
    }

    public function post(string $path, \Closure $closure = null)
    {
        return $this->router->post($path, $closure);
    }

    public function head(string $path, \Closure $closure = null)
    {
        return '';
    }

    public function options(string $path, \Closure $closure = null)
    {
        return '';
    }

    public function delete(string $path, \Closure $closure = null)
    {
        return $this->router->delete($path, $closure);
    }

    public function patch(string $path, \Closure $closure = null)
    {
        return $this->router->pathch($path, $closure);
    }

    public function put(string $path, \Closure $closure = null)
    {
        return $this->router->put($path, $closure);
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param string $path
     * @param string $method
     * @return false|Cute\Route
     */
    private function _match(string $path, string $method = 'get')
    {
        return $this->router->match($path, $method);
    }

    /**
     * 解析路由
     * @param string $path
     * @param string $method
     * @return mixed|array
     */
    public function dispatch(string $path, string $method = 'get')
    {
        $route = $this->_match($path, $method);

        $obj = [];

        if ($route) {
            $dispatch = $route->getStorage();
            $call = $dispatch['dispatch'] ?? [];
            $before = $dispatch['before'] ?? [];
            $after = $dispatch['after'] ?? [];
            $type = $dispatch['type'] ?? Producer::PRODUCER_SYNC;
            $params = $route->getParams();
            if (is_array($call) and class_exists($call[0])) {
                $params = $params === null ? [] : $params;
                return $this->_runProducer($call, $params, $before, $after, $type);
            }
            if (is_string($call)) {
                $obj = [$call];
            }
            return $obj;
        } else {
            return ['code' => 404];
        }

    }

    /**
     * 執行任務
     * @param array $call
     * @param array $before
     * @param array $params
     * @param array $after
     * @param string $type
     * @return mixed
     * @throws
     */
    private function _runProducer($call, $params, $before, $after, string $type)
    {
        /** @var $obj */
        $controller = Core::getInstance()->getContainer()->build($call[0]);
        $producer = Producer::getProducer($type);
        $this->_addAction($producer, $before, 'Before');
        $this->_addAction($producer, $after, 'After');
        $producer->addProducer($controller, $call[1], $params);
        return $producer->run();
    }

    /**
     * 添加事物
     * @param IProducer $producer
     * @param $action
     * @param string $event
     */
    private function _addAction(IProducer $producer, $action, $event = 'After')
    {
        $func = 'add' . ucfirst($event);
        if (method_exists($producer, $func)) {
            if (isset($action[0]) and class_exists($action[0])) {
                $producer->$func(function () use ($action) {
                    $obj = Core::getInstance()->getContainer()->build($action[0]);
                    $obj->{$action[1]}();
                });
            }
            if ($action instanceof \Closure) {
                $producer->$func($action);
            }
        }
    }
}