<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 22:48
 */

namespace fastwork;


use fastwork\exception\ClassNotFoundException;
use fastwork\exception\MethodNotFoundException;
use fastwork\facades\Config;
use fastwork\facades\Env;
use fastwork\router\RouterParse;

class Route
{
    private $routes = array();

    public function dispath(Request $request)
    {
        $path = $request->path();
        $route = $this->match($path, $request->method());
        if ($route) {
            $className = $route->getStorage(); // 123
            $params = $route->getParams();
            //没有路由配置或者配置不可执行，则走默认路由
            if (is_string($className)) {
                return $this->normal($className, $request, $params);
            } elseif (is_callable($className)) {
                return $className();
            }
        }
        return $this->normal($path, $request);
    }

    /**
     * @param $path
     * @param Request $request
     * @param array $param
     * @return mixed
     * @throws \ReflectionException
     */
    public function normal($path, Request $request, $query_string = [])
    {
        $config = Config::get('app');
        $module = $config['default_module'];
        $controller = $config['default_controller'];
        $action = $config['default_action'];
        //默认访问 controller/index.php 的 index方法
        $path = rtrim(ltrim($path, '/'));
        $params = [];
        if (!empty($path)) {
            $maps = explode('/', $path);
            $mapsNumber = count($maps);
            $param = array_slice($maps, 2);
            if ($mapsNumber == 2) {
                $controller = $maps[0];
                (isset($maps[1]) && !empty($maps[1])) && $action = $maps[1];
            } else if ($mapsNumber >= 3) {
                $module = $maps[0];
                $controller = $maps[1];
                (isset($maps[2]) && !empty($maps[2])) && $action = $maps[2];
                $param = array_slice($maps, 3);
            } else {
                $module = $maps[0];
            }
            //多余的路由转参数
            if (!empty($param)) {
                foreach ($param as $key => $value) {
                    if ($key % 2 == 0) {
                        $params[$value] = $key;
                    } else {
                        $k = array_search($key - 1, $params);
                        isset($params[$k]) && $params[$k] = $value;
                    }
                }
            }
        }
        /**
         * 合并所有参数
         */
        if (!is_null($query_string)) {
            $params = array_merge($params, $query_string);
        }
        $app_namespace = Env::get('app_namespace');

        $module = strtolower($module);
        $controller = ucfirst($controller);
        $action = strtolower($action);

        $app = Container::get('fastwork');
        $classname = "\\{$app_namespace}\\{$module}\\controller\\{$controller}";
        $request->setAction($action)->setController($controller)->setModule($module)->setParam($params);


        $realmvc = "{$module}/{$controller}/{$action}";
        if (!class_exists($classname)) {
            throw  new  ClassNotFoundException('class not exit:' . $realmvc);
        }
        //反射依赖注入
        $reflect = new \ReflectionClass($classname);
        $constructor = $reflect->getConstructor();
        $args = [];
        if ($constructor) {
            $args = $app->bindParams($constructor, []);
        }
        if (!$reflect->hasMethod($action)) {
            if (!$reflect->hasMethod('_empty')) {
                throw new MethodNotFoundException('method not exit:' . $realmvc);
            }
            $action = '_empty';
        }
        $method = $reflect->getMethod($action);
        if (!$method->isPublic()) {
            throw new MethodNotFoundException('method not exit:' . " {$realmvc}");
        }
        $content = $app->invokeMethod([$reflect->newInstanceArgs($args), $action], $params);
        return $content;
    }

    /**
     * 添加get参数路由
     *
     * @param  String $uri 路由匹配的URI
     * @param  [Mix] [Mix] $storage 你要存入的任意类型
     * @param  String $name 路由名
     * @return RouterParse
     */
    public function get($uri, $storage, $name = null)
    {
        return $this->add($uri, $storage, $name, 'GET');
    }

    /**
     * 添加post参数路由
     *
     * @param  String $uri 路由匹配的URI
     * @param  [Mix] $storage 你要存入的任意类型
     * @param  String $name 路由名
     * @return RouterParse
     */
    public function post($uri, $storage, $name = null)
    {
        return $this->add($uri, $storage, $name, 'POST');
    }

    /**
     * 添加新的路由匹配
     * @param $uri
     * @param $storage
     * @param null $name
     * @param null $methods
     * @return RouterParse
     */
    public function add($uri, $storage, $name = null, $methods = null)
    {
        $route = new RouterParse($uri, $storage, $methods);
        if ($name !== null) {
            $this->routes[$name] = $route;
        } else {
            $this->routes[] = $route;
        }
        return $route;
    }

    /**
     * 按照传入的规则从添加的路由中找到匹配路由
     * @param  String $uri 要匹配的URL
     * @param  String $method 匹配的HTTP方法
     * @return bool|mixed
     */
    public function match($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route->match($uri, $method)) { //调用每个对象Route查询是非匹配
                return $route;
            }
        }
        //没有找到匹配的路由
        return false;
    }
}