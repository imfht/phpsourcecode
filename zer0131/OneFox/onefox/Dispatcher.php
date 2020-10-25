<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc 调度操作类
 */

namespace onefox;

class Dispatcher {

    private static $_uri = '';
    private static $_defaultModule = DEFAULT_MODULE;
    private static $_defaultController = DEFAULT_CONTROLLER;
    private static $_defaultAction = DEFAULT_ACTION;
    private static $_currentModule = null;
    private static $_currentController = null;
    private static $_currentAction = null;

    public static function dipatcher() {
        //处理url
        if (isset($_SERVER['PATH_INFO'])) {
            self::$_uri = $_SERVER['PATH_INFO'];
        } else {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (0 === strpos($requestUri, $_SERVER['SCRIPT_NAME'])) {
                self::$_uri = substr($requestUri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (0 === strpos($requestUri, dirname($_SERVER['SCRIPT_NAME']))) {
                self::$_uri = substr($requestUri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            } else {
                self::$_uri = $requestUri;
            }
        }

        //去除'/'
        self::$_uri = trim(self::$_uri, '/');

        self::_httpRout();
    }

    /**
     * 处理uri
     */
    private static function _httpRout() {
        $uri = self::$_uri;
        $moduleName = null;
        if ($uri == '') {
            if (MODULE_MODE) {
                $moduleName = self::$_defaultModule;
            }
            $controllerName = self::$_defaultController;
            $actionName = self::$_defaultAction;
        } else {
            $uriArr = explode('/', $uri);

            if (MODULE_MODE) {
                $moduleName = array_shift($uriArr);
                if (count($uriArr) > 0) {
                    $controllerName = array_shift($uriArr);
                    if (count($uriArr) > 0) {
                        $actionName = array_shift($uriArr);
                    } else {
                        $actionName = self::$_defaultAction;
                    }
                } else {
                    $controllerName = self::$_defaultController;
                    $actionName = self::$_defaultAction;
                }
            } else {
                $controllerName = array_shift($uriArr);
                $actionName = array_shift($uriArr);
                $actionName = $actionName !== null ? $actionName : self::$_defaultAction;
            }

            //处理剩余参数
            if (count($uriArr) > 0) {
                $params = [];
                preg_replace_callback('/(\w+)\/([^\/]+)/', function ($match) use (&$params) {
                    $params[$match[1]] = $match[2];
                }, implode('/', $uriArr));// 解析剩余的URL参数
                Request::setParams($params, 'get');
            }
        }

        //过滤并赋值
        $moduleName = C::filterChars($moduleName);
        $controllerName = C::filterChars($controllerName);
        $actionName = C::filterChars($actionName);

        self::$_currentModule = $moduleName;
        self::$_currentController = $controllerName;
        self::$_currentAction = $actionName;
    }

    public static function getModuleName() {
        if (is_null(self::$_currentModule)) {
            self::$_currentModule = '';
        }
        return strtolower(self::$_currentModule);
    }

    public static function getControllerName() {
        return ucfirst(strtolower(self::$_currentController));
    }

    public static function getActionName() {
        return self::$_currentAction;
    }
}


