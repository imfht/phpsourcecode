<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zswin.cn
// +----------------------------------------------------------------------
/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string       $name 格式 [模块名]/接口名/方法名
 * @param  array|string $vars 参数
 */
function api($name, $vars = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'Common';
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;
    if (is_string($vars)) {
        parse_str($vars, $vars);
    }
    return call_user_func_array($callback, $vars);
}
function callApi($apiName, $args = array())
{
    //
    $paths = explode('/', $apiName);
    $controllerName = "Api\\Controller\\$paths[0]Controller";
    $controller = new $controllerName();
    $controller->setInternalCallApi();
    $function = $paths[1];
    $method = new ReflectionMethod($controllerName, $function);
    try {
        $method->invokeArgs($controller, $args);
    } catch (Api\Exception\ReturnException $ex) {
        return $ex->getResult();
    }
}

function apiToAjax($result)
{
    $result['status'] = $result['success'];
    $result['info'] = $result['message'];
    unset($result['success']);
    unset($result['message']);
    return $result;
}

function ensureApiSuccess($apiResult)
{
    if (!$apiResult['success']) {
        api_show_error($apiResult['message']);
    }
}

/**
 * 显示错误消息，根据调用方式。如果是ajax调用，则返回ajax错误信息；
 * 如果是直接页面访问的话，直接显示错误消息
 * @param $message
 */
function api_show_error($message, $extra = array())
{
    if (IS_AJAX) {
        api_show_error_json($message, $extra);
    } else {
        api_show_error_html($message, $extra);
    }
}

function api_show_error_json($message, $extra = array())
{
    //生成错误信息
    $json['status'] = false;
    $json['info'] = $message;
    $json = array_merge($json, $extra);

    //返回
    header('Content-Type: application/json');
    echo json_encode($json);
}

function api_show_error_html($message, $extra = null)
{
    class EnsureApiSuccessController extends Think\Controller
    {
        public function showError($message)
        {
            $this->error($message);
        }
    }

    $controller = new EnsureApiSuccessController();
    $controller->showError($message);
}