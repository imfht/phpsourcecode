<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/15
 * Time: 9:13
 * ----------------------------------------------------------------------
 */

namespace app\admin\controller;

use think\Controller;

/**
 * 后台公共控制器
 * @package app\admin\controller
 */
class Base extends Controller
{
    protected $aid;
    protected $isLogin = false;     //登录状态
    protected $adminAuthRule;
    protected $module;
    protected $controller;
    protected $action;
    protected $roleId;
    protected $adminName;

    public function initialize()
    {
        // 判断是否安装OCenter
        if (!is_file(APP_PATH . 'admin/command/Install/install.lock'))
        {
            $this->redirect(url('install/Install/index'));
        }
        //登录验证
        if (!$this->isLogin()) {
//            $this->error('请登录后再试', 'admin/login/login');
            $this->redirect('admin/login/login');
        }
        //数据初始化
        $this->aid = get_aid();
        $this->module = $this->request->module();
        $this->controller = $this->request->controller();
        $this->action = $this->request->action();
        $admin = session('admin_auth');
        $this->adminName = $admin['username'];
        $group = model('admin/AdminAuthGroup')->where('status', 1)->column('title', 'id');
        $adminRole = explode(',', $admin['role_id']);
        $currentRoleId = $admin['current_role'];
        $this->roleId = $currentRoleId;
        $currentRoleRules = model('admin/AdminAuthGroup')->where('id', $currentRoleId)->value('rules');
        if ($currentRoleRules !== '*') {
            $currentRoleRules = explode(',', $currentRoleRules);
        }
        //菜单机制
        $menuList = cache('menu');
        if (!$menuList) {
            $map1[] = ['is_menu', '=', 1];
            $map1[] = ['is_show', '=', 1];
            $map1[] = ['status', '=', 1];
            $map1[] = ['module', '=', 'admin'];
            if (is_array($currentRoleRules)) {
                $map1[] = ['id', 'in', $currentRoleRules];
            }
            $menu = model('admin/AdminAuthRule')
                ->where($map1)
                ->order('sort asc')
                ->select()
                ->toArray();
            $menuList = list_to_tree($menu);
            //菜单写入缓存
            cache('menu', $menuList);
        }
        //导航栏
        $navList = cache('nav');
        if (!$navList) {
            $map2[] = ['is_menu', '=', 1];
            $map2[] = ['is_show', '=', 1];
            $map2[] = ['status', '=', 1];
            $map2[] = ['module', '<>', 'admin'];
            if (is_array($currentRoleRules)) {
                $map2[] = ['id', 'in', $currentRoleRules];
            }
            $nav = model('admin/AdminAuthRule')
                ->where($map2)
                ->order('sort asc')
                ->select()
                ->toArray();
            $navList = list_to_tree($nav);
            //菜单写入缓存
            cache('nav', $navList);
        }
        $this->assign('menu', $menuList);
        $this->assign('nav', $navList);
        //通用数据渲染
        $this->assign('adminRole', $adminRole);
        $this->assign('group', $group);
        $this->assign('admin', $admin);
        //模型初始化
        $this->adminAuthRule = model('admin/AdminAuthRule');
        //权限校验
        $this->checkAuth();
    }

    /**
     * 检测是否登录
     * @return boolean
     */
    public function isLogin()
    {
        if ($this->isLogin) {
            return true;
        }
        $admin = session('admin_auth');
        if (!$admin) {
            return false;
        }
        $this->isLogin = true;
        return true;
    }

    /**
     * 权限校验
     * @author:wdx(wdx@ourstu.com)
     */
    protected function checkAuth()
    {
        $model = strtolower($this->request->module());
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        $rule = $this->adminAuthRule->where('name', $model . '/' . $controller . '/' . $action)->where('status', 1)->find();
        if ($rule) {
            $admin = session('admin_auth');
            if ($admin['uid'] === 1) {
                return true;
            }
            $hasRule = $admin['rules'];
            if ($hasRule[0] === '*' || in_array($rule['id'], $hasRule)) {
                return true;
            } else {
                $this->error('越权访问');
            }
        } else {
            return true;
        }
    }

    /**
     * 获取当前请求的Accept头信息
     * @return string
     */
    protected function getAcceptType()
    {
        $type = array(
            'html' => 'text/html,application/xhtml+xml,*/*',
            'xml' => 'application/xml,text/xml,application/x-xml',
            'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
            'js' => 'text/javascript,application/javascript,application/x-javascript',
            'css' => 'text/css',
            'rss' => 'application/rss+xml',
            'yaml' => 'application/x-yaml,text/yaml',
            'atom' => 'application/atom+xml',
            'pdf' => 'application/pdf',
            'text' => 'text/plain',
            'png' => 'image/png',
            'jpg' => 'image/jpg,image/jpeg,image/pjpeg',
            'gif' => 'image/gif',
            'csv' => 'text/csv'
        );

        foreach ($type as $key => $val) {
            $array = explode(',', $val);
            foreach ($array as $k => $v) {
                if (stristr($_SERVER['HTTP_ACCEPT'], $v)) {
                    return $key;
                }
            }
        }
        return false;
    }

    // 发送Http状态信息
    protected function sendHttpStatus($code)
    {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        } else {
            // 找不到code码时为400
            $code = 400;
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
            // 确保FastCGI模式下正常
            header('Status:' . $code . ' ' . $_status[$code]);
        }
    }

    /**
     * 编码数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @return void
     */
    protected function encodeData($data, $type = '')
    {
        if (empty($data)) return '';
        if ('json' == $type) {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = json_encode($data);
        } elseif ('xml' == $type) {
            // 返回xml格式数据
            $data = xml_encode($data);
        } elseif ('php' == $type) {
            $data = serialize($data);
        }
        // 默认直接输出
        $this->setContentType($type);
        header('Content-Length: ' . strlen($data));
        return $data;
    }

    /**
     * 设置页面输出的CONTENT_TYPE和编码
     * @access public
     * @param string $type content_type 类型对应的扩展名
     * @param string $charset 页面输出编码
     * @return void
     */
    public function setContentType($type, $charset = '')
    {
        if (headers_sent()) return;
        if (empty($charset)) $charset = config('DEFAULT_CHARSET');
        $type = strtolower($type);
        if (isset($this->allowOutputType[$type])) //过滤content_type
            header('Content-Type: ' . $this->allowOutputType[$type] . '; charset=' . $charset);
    }

    /**
     * 输出返回数据
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type 返回类型 JSON XML
     * @param integer $code HTTP状态
     * @return void
     */
    protected function response($data, $type = '', $code = 200)
    {
        $this->sendHttpStatus($code);
        exit($this->encodeData($data, strtolower($type)));
    }

    /**
     * ajax成功返回
     * @param $result
     * @author:wdx(wdx@ourstu.com)
     */
    public function ajaxSuccess($result)
    {
        $this->apiSuccess('返回成功', $result);
    }

    /**
     * ajax失败返回
     * @param $args
     * @author:wdx(wdx@ourstu.com)
     */
    public function ajaxError($args)
    {
        foreach ($args as $key => $value) {
            if (empty($value))
                $this->apiError($key . '参数错误');
        }
    }

    /**
     * api返回
     * @author:wdx(wdx@ourstu.com)
     */
    protected function apiReturn()
    {
        $code = 200;
        $args = func_get_args();
        $this->apiResponse($args, $code);
    }

    /**
     * api成功返回
     * @author:wdx(wdx@ourstu.com)
     */
    protected function apiSuccess()
    {
        $args = func_get_args();
        $code = 200;
        $this->apiResponse($args, $code);
    }

    /**
     * api失败返回
     * @author:wdx(wdx@ourstu.com)
     */
    protected function apiError()
    {
        $args = func_get_args();
        $code = 400;
        $this->apiResponse($args, $code);
    }

    /**
     * api通用返回
     * @param $args
     * @param int $code
     * @author:wdx(wdx@ourstu.com)
     */
    protected function apiResponse($args, $code = 200)
    {
        $rs = array();
        foreach ($args as $key => $v) {
            if (is_array($v)) {
                if (isset($rs['data'])) {
                    $rs['data_' . $key] = $v;
                } else {
                    $rs['data'] = $v;
                }
            } elseif (is_int($v)) {
                $code = $v;
            } else {
                $rs['info'] = $v;
            }
        }
        $rs['code'] = $code;
        $this->response($rs, 'json', $code);
    }
}