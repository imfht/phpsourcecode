<?php

namespace App\Router;

use Swoole\IFace\Router;
use Swoole\Tool;

/**
 * 模块路由器
 * Class ModuleRouter.
 */
class ModuleRouter implements Router
{
    public function handle(&$uri)
    {
        $request = \Swoole::$php->request;
        //默认模块、控制器、方法
        $array = \Swoole::$php->config['module']['default'];
        if (isset($array['directory']) && empty($array['directory'])) {
            unset($array['directory']);
        }
        //模块列表
        $moduleList = \Swoole::$php->config['module']['list'];

        if (!empty($request->get['m'])) {
            $array['directory'] = $request->get['m'];
        }
        if (!empty($request->get['c'])) {
            $array['controller'] = $request->get['c'];
        }
        if (!empty($request->get['v'])) {
            $array['view'] = $request->get['v'];
        }
        //切割字符串
        $request_uri = explode('/', $uri, 4);
        //最后URI参数
        $module = isset($request_uri[0]) && $request_uri[0] ? ucwords($request_uri[0]) : '';
        if (in_array($module, $moduleList)) {//模块分组
            $array['directory'] = $request_uri[0];
            if (isset($request_uri[1]) && $request_uri[1]) {
                $array['controller'] = $request_uri[1];
            }
            if (isset($request_uri[2]) && $request_uri[2]) {
                $array['view'] = $request_uri[2];
            }
            if (count($request_uri) < 3) {
                return $array;
            }
            Tool::$url_prefix = '';
            if (isset($request_uri[3])) {
                $request_uri[3] = trim($request_uri[3], '/');
                $_id            = str_replace('.html', '', $request_uri[2]);
                if (is_numeric($_id)) {
                    $request->get['id'] = $_id;
                } else {
                    Tool::$url_key_join   = '-';
                    Tool::$url_param_join = '-';
                    Tool::$url_add_end    = '.html';
                    Tool::$url_prefix     = WEBROOT . "/{$request_uri[0]}/$request_uri[1]/{$request_uri[2]}/";
                    Tool::url_parse_into($request_uri[3], $request->get);
                }
                $_REQUEST = $request->request = array_merge($request->request, $request->get);
                $_GET     = $request->get;
            }
        } else {//按模块分组
            //重新切割
            $request_uri = explode('/', $uri, 3);
            //无模块分组
            if (isset($request_uri[0]) && $request_uri[0]) {
                $array['controller'] = $request_uri[0];
            }
            if (isset($request_uri[1]) && $request_uri[1]) {
                $array['view'] = $request_uri[1];
            }
            if (count($request_uri) < 2) {
                return $array;
            }
            Tool::$url_prefix = '';
            if (isset($request_uri[2])) {
                $request_uri[2] = trim($request_uri[2], '/');
                $_id            = str_replace('.html', '', $request_uri[2]);
                if (is_numeric($_id)) {
                    $request->get['id'] = $_id;
                } else {
                    Tool::$url_key_join   = '-';
                    Tool::$url_param_join = '-';
                    Tool::$url_add_end    = '.html';
                    Tool::$url_prefix     = WEBROOT . "/{$request_uri[0]}/$request_uri[1]/";
                    Tool::url_parse_into($request_uri[2], $request->get);
                }
                $_REQUEST = $request->request = array_merge($request->request, $request->get);
                $_GET     = $request->get;
            }
        }

        return $array;

        //未命中路由器，返回false，继续执行下一个路由器
        return false;
    }
}
