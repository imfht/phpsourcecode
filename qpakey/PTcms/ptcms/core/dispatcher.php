<?php

/**
 * @Author: 杰少Pakey
 * @Email : admin@ptcms.com
 * @File  : dispatcher.php
 */
class PT_Dispatcher extends PT_Base
{
    
    // 入口文件
    public function run()
    {
        // 暂时去掉post设置参数
        //if (!empty($_POST['s'])) $_GET['s'] = $_POST['s'];
        //if (!empty($_POST['m'])) $_GET['m'] = $_POST['m'];
        //if (!empty($_POST['c'])) $_GET['c'] = $_POST['c'];
        //if (!empty($_POST['a'])) $_GET['a'] = $_POST['a'];
        // 模块映射域名
        if ($module_domain = $this->config->get('module_domain', '')) {
            $host = $_SERVER['HTTP_HOST'];
            if (in_array($host, $module_domain)) {
                $_GET['m']=array_search($host, $module_domain);
            }
        }
        
        if (empty($_GET['s'])) {
            //设置默认值 不带SupeVar 为变量模式
            $_GET['m'] = empty($_GET['m']) ? $this->config->get('default_module', 'index') : $_GET['m'];
            $_GET['c'] = empty($_GET['c']) ? $this->config->get('default_controller', 'index') : $_GET['c'];
            $_GET['a'] = empty($_GET['a']) ? $this->config->get('default_action', 'index') : $_GET['a'];
        } else {
            $_GET['s'] = trim($_GET['s'], '/');//去除左右的/防止干扰
            $this->router();//路由校验
            $this->parseSuperVar();//解析超级变量
        }
        $_GET['f'] = empty($_GET['f']) ? $this->config->get('default_format', 'html') : $_GET['f'];
        // 后缀过滤
        if (!in_array($_GET['f'], ['html', 'json', 'jsonp', 'xml'])) {
            $_GET['f'] = 'html';
        }
        //module映射
        $mapModule = $this->config->get('map_module', []);
        if (isset($mapModule[$_GET['m']])) {
            halt('当前模块已经改名', __FILE__, __LINE__ - 1);
        } elseif (in_array($_GET['m'], $mapModule)) {
            $_GET['_m'] = $_GET['m'];
            $_GET['m']  = array_search($_GET['m'], $mapModule);
        }
        //过滤xss及参数前后空白
        foreach ($_GET as $k=>$v) {
            $_GET[$k] = trim(strip_tags($v));
        }
        $_REQUEST = array_merge($_GET, $_POST);
    }
    
    // 解析超级变量
    public function parseSuperVar()
    {
        if (strpos($_GET['s'], '.')) {
            $param     = explode('.', $_GET['s'], 2);
            $_GET['f'] = $param['1'];
            $param     = explode('/', $param['0']);
        } else {
            $param = explode('/', $_GET['s']);
        }
        if(empty($_GET['m'])){
            $var['m'] = isset($param['0']) ? array_shift($param) : $this->config->get('default_module', 'index');
        }
        $var['c'] = isset($param['0']) ? array_shift($param) : $this->config->get('default_controller', 'index');
        $var['a'] = isset($param['0']) ? array_shift($param) : $this->config->get('default_action', 'index');
        while ($k = each($param)) {
            $var[$k['value']] = current($param);
            next($param);
        };
        $_GET = array_merge($var, $_GET);
    }
    
    // 解析路由
    public function router()
    {
        if ($router = $this->config->get('url_router')) {
            foreach ($router as $rule => $url) {
                if (preg_match('{' . $rule . '}isU', $_GET['s'], $match)) {
                    unset($match['0']);
                    if (0 === strpos($url, '/') || 0 === stripos($url, 'http://')) { // 路由重定向跳转
                        header("Location: $url", true, 301);
                        exit;
                    } elseif (strpos($url, '?')) {
                        list($url, $query) = explode('?', $url);
                    }
                    $_GET['s'] = rtrim($url, '/');
                    if ($match && !empty($query)) {//组合后面的参数
                        $param = explode('&', $query);
                        
                        if (count($param) == count($match) && $var = array_combine($param, $match)) {
                            $_GET = array_merge($_GET, $var);
                        }
                    }
                    break;
                }
            }
        }
    }
}

class Dispatcher extends PT_Dispatcher
{
    
}