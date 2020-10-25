<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

namespace Yan\Core;

use FastRoute;

/**
 * Class Dispatcher
 * @package Yan\Core
 */
class Dispatcher
{
    /** @var string 匹配路径 */
    protected static $handler = '';
    /** @var string 请求控制器类名 */
    public static $controller = '';
    /** @var string 请求方法 */
    public static $method = '';
    /** @var string 请求控制器short name */
    public static $controllerShortName = '';

    public static function initialize()
    {
        $rules = static::getRules();
        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($rules) {
            foreach ($rules as $route => $rule) {
                $handler = $rule['controller'] . '.' . ($rule['method'] ?? '');
                $r->addRoute($rule['request_method'], $route, $handler);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        Log::info('request uri=' . $uri . ' method=' . $httpMethod);

        $uri = substr($uri, strpos($uri, '/interface.php') + 14) ?: $uri;

        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rtrim($uri, '\\/');
        $uri = empty($uri) ? '/' : $uri;

        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        $code = ReturnCode::OK;
        $msg = '';
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                //利用自定义路由策略再进行一次匹配
                $route = static::router();
                if (empty($route['controller']) || empty($route['method'])) {
                    $code = ReturnCode::REQUEST_404;
                    $msg = '404 Not Found';
                    header("HTTP/1.1 404 Not Found");
                } else {
                    $namespace = Config::get('namespace');
                    $controller = $namespace . "\\Controller\\" . ucfirst($route['controller']) . "Controller";
                    $method = $route['method'];
                    static::$handler = $controller . '.' . $method;
                }
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                header("HTTP/1.1 405 Method Not Allowed");
                $code = ReturnCode::REQUEST_METHOD_NOT_ALLOW;
                $msg = 'Method Not Allowed';
                break;
            case FastRoute\Dispatcher::FOUND:
                static::$handler = $routeInfo[1];
                $vars = $routeInfo[2];
                break;
        }
        if ($code != ReturnCode::OK) {
            $result = genResult($code, $msg, []);
            showResult($result);
        }
    }


    /**
     * handle uri
     *
     * @return array
     */
    protected static function router(): array
    {
        $urlPath = $_SERVER['REQUEST_URI'];
        $filePath = $_SERVER['PHP_SELF'];
        $documentPath = $_SERVER['DOCUMENT_ROOT'];
        $appPath = str_replace($documentPath, '', $filePath);
        $appPathArr = explode(DIRECTORY_SEPARATOR, $appPath);
        //get the real controller and method
        foreach ($appPathArr as $k => $v) {
            if ($v) {
                $urlPath = preg_replace('/^\/' . $v . '\/?/', '/', $urlPath, 1);
            }
        }
        $urlPath = preg_replace('/^\//', '', $urlPath, 1); //ltrim($urlPath,'/')
        $appPathArr = explode('/', $urlPath);
        //trim the parameters
        if (!empty($appPathArr[0])) {
            $appPathArr[0] = preg_replace('/(\?.*)$/', '', $appPathArr[0]);
        }
        if (!empty($appPathArr[1])) {
            $appPathArr[1] = preg_replace('/(\?.*)$/', '', $appPathArr[1]);
        }
        $appRequest = array(
            'controller' => $appPathArr[0] ?? '',
            'method' => $appPathArr[1] ?? '',
        );
        return $appRequest;
    }

    public static function getHandler()
    {
        return static::$handler;
    }

    /**
     * 分发请求
     * @return array
     */
    public static function dispatch(): array
    {
        $handlerArr = explode('.', static::$handler);
        if (empty($handlerArr[0])) {
            show404();
        }

        if (!class_exists($handlerArr[0]) || !method_exists($handlerArr[0], $handlerArr[1])) {
            show404();
        }
        $class = new \ReflectionClass($handlerArr[0]);
        $method = $class->getMethod($handlerArr[1]);
        if (!$method->isPublic()) {
            show404();
        }
        self::$controller = $handlerArr[0];
        self::$method = $handlerArr[1];
        self::$controllerShortName = getClassName($handlerArr[0]);

        return $handlerArr;
    }

    /**
     * 获取路由规则
     * @return array
     */
    public static function getRules(): array
    {
        return Config::get('route');
    }

}