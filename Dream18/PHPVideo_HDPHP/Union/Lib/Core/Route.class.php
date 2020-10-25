<?php
// .-----------------------------------------------------------------------------------
// |  Software: [HDPHP framework]
// |   Version: 2013.01
// |      Site: http://www.hdphp.com
// |-----------------------------------------------------------------------------------
// |    Author: 向军 <2300071698@qq.com>
// | Copyright (c) 2012-2013, http://houdunwang.com. All Rights Reserved.
// |-----------------------------------------------------------------------------------
// |   License: http://www.apache.org/licenses/LICENSE-2.0
// '-----------------------------------------------------------------------------------
/**
 * URL路由处理类
 * @package     Core
 * @author      后盾向军 <houdunwangxj@gmail.com>
 */
final class Route
{
    /**
     * 根据不同url处理方式，得到Url参数
     */
    static public function parseUrl()
    {
        //请求内容
        if (C('URL_TYPE') == 3 && isset($_GET[C("PATHINFO_VAR")])) {
            $query = $_GET[C("PATHINFO_VAR")];
        } else if (C('URL_TYPE') == 1 && isset($_SERVER['PATH_INFO'])) {
            $query = $_SERVER['PATH_INFO'];
        } else if (isset($_SERVER['PATH_INFO'])) {
            $query = $_SERVER['PATH_INFO'];
        } else {
            $query = $_SERVER['QUERY_STRING'];
        }
        //分析路由 && 清除伪静态后缀
        $url = self::parseRoute($query);
        //拆分后的GET变量
        $gets = '';
        if ((C('URL_TYPE') == 1 && isset($_SERVER['PATH_INFO'])) || (C('URL_TYPE') == 3 && isset($_GET[C("PATHINFO_VAR")]))) {
            $url = str_replace(array('&', '='), C("PATHINFO_DLI"), $url);
            $args = explode(C("PATHINFO_DLI"), $url);
            //模块
            if (empty($args[0])) {
                $_GET[C("VAR_MODULE")] = C("DEFAULT_MODULE");
            } else {
                if ($args[0] == C("VAR_MODULE")) {
                    $_GET[$args[0]] = $args[1];
                    array_shift($args);
                    array_shift($args);
                } else {
                    $_GET[C("VAR_MODULE")] = $args[0];
                    array_shift($args);
                }
            }
            //控制器
            if (empty($args[0])) {
                $_GET[C('VAR_CONTROLLER')] = C('DEFAULT_CONTROLLER');
            } else {
                if ($args[0] == C('VAR_CONTROLLER')) {
                    $_GET[$args[0]] = $args[1];
                    array_shift($args);
                    array_shift($args);
                } else {
                    $_GET[C('VAR_CONTROLLER')] = $args[0];
                    array_shift($args);
                }
            }
            //动作
            if (empty($args[0])) {
                $_GET[C('VAR_ACTION')] = C('DEFAULT_ACTION');
            } else {
                if ($args[0] == C('VAR_ACTION')) {
                    $_GET[$args[0]] = $args[1];
                    array_shift($args);
                    array_shift($args);
                } else {
                    $_GET[C('VAR_ACTION')] = $args[0];
                    array_shift($args);
                }
            }
            //其他$_GET数据
            if (!empty($args[0]) && count($args) % 2 == 0) {
                $count = count($args);
                for ($i = 0; $i < $count;) {
                    $_GET[$args [$i]] = isset($args [$i + 1]) ? $args [$i + 1] : '';
                    $i += 2;
                }
            }
        } else {
            //解析URL
            parse_str($url, $gets);
            $_GET = array_merge($_GET, $gets);
            //模块
            if (!isset($_GET[C("VAR_MODULE")])) {
                $_GET[C("VAR_MODULE")] = C("DEFAULT_MODULE");
            }
            //控制器
            if (!isset($_GET[C("VAR_CONTROLLER")])) {
                $_GET[C('VAR_CONTROLLER')] = C('DEFAULT_CONTROLLER');
            }
            //动作方法
            if (!isset($_GET[C("VAR_ACTION")])) {
                $_GET[C('VAR_ACTION')] = C('DEFAULT_ACTION');
            }
        }
        //转模块名大小写
        $_GET[C('VAR_MODULE')] = ucwords($_GET[C('VAR_MODULE')]);
        //以下划线分隔的模块名称改为pascal命名如hdphp_user=>HDPhpUser
        $_GET[C('VAR_CONTROLLER')] = str_replace('!', '', ucwords(str_replace('_', '!', $_GET[C('VAR_CONTROLLER')])));
        //兼容模式删除其变量
        if (C('URL_TYPE') == 2) {
            unset($_GET[C('PATHINFO_VAR')]);
        }
        $_REQUEST = array_merge($_REQUEST, $_GET);
        //设置常量
        self::setConst();
    }

    /**
     * 设置常量
     */
    static private function setConst()
    {
        //域名
        $host = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        defined('__HOST__') or define("__HOST__", C("HTTPS") ? "https://" : "http://" . $host);
        //网站根-不含入口文件
        $script_file = rtrim($_SERVER['SCRIPT_NAME'], '/');
        $root = rtrim(dirname($script_file), '/');
        defined('__ROOT__') or define("__ROOT__", __HOST__ . ($root == '/' || $root == '\\' ? '' : $root));
        //网站根-含入口文件 开启伪静态时去除入口文件
        if (C('URL_REWRITE')) {
            //删除入口文件
            $scriptName = preg_replace('/\/?[a-z]+\.php/i', '', $_SERVER['SCRIPT_NAME']);
            defined('__WEB__') or define("__WEB__", __HOST__ . $scriptName);
        } else {
            defined('__WEB__') or define("__WEB__", __HOST__ . $_SERVER['SCRIPT_NAME']);
        }
        //完整URL地址
        defined('__URL__') or define("__URL__", __HOST__ . '/' . trim($_SERVER['REQUEST_URI'], '/'));
        //应用URL地址
        defined('__APP__') or define("__APP__", __ROOT__ . '/' . basename(APP_PATH));
        //公共目录
        defined('__COMMON__') or define("__COMMON__", __APP__ . '/Common');
        //框架目录相关URL
        defined('__HDPHP__') or define("__HDPHP__", __HOST__ . '/' . trim(str_ireplace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), "", HDPHP_PATH), '/'));
        defined('__HDPHP_DATA__') or define("__HDPHP_DATA__", __HDPHP__ . '/Data');
        defined('__HDPHP_EXTEND__') or define("__HDPHP_EXTEND__", __HDPHP__ . '/Extend');
        //应用
        defined('APP') or define('APP', basename(APP_PATH));
        //模块
        defined('MODULE') or define("MODULE", ucfirst($_GET[C('VAR_MODULE')]));
        //控制器
        defined('CONTROLLER') or define("CONTROLLER", ucfirst($_GET[C('VAR_CONTROLLER')]));
        //方法
        defined('ACTION') or define("ACTION", $_GET[C('VAR_ACTION')]);
        // URL类型    1:pathinfo  2:普通模式  3:兼容模式
        switch (C("URL_TYPE")) {
            //普通模式
            case 2:
                defined('__MODULE__') or define("__MODULE__", __WEB__ . '?' . C('VAR_MODULE') . '=' . MODULE);
                defined('__CONTROLLER__') or define("__CONTROLLER__", __MODULE__ . '&' . C('VAR_CONTROLLER') . '=' . CONTROLLER);
                defined('__ACTION__') or define("__ACTION__", __CONTROLLER__ . '&' . C('VAR_ACTION') . '=' . ACTION);
                break;
            //兼容模式
            case 3:
                defined('__MODULE__') or define("__MODULE__", __WEB__ . '?' . C("PATHINFO_VAR") . '=/' . MODULE);
                defined('__CONTROLLER__') or define("__CONTROLLER__", __MODULE__ . '/' . CONTROLLER);
                defined('__ACTION__') or define("__ACTION__", __CONTROLLER__ . '/' . ACTION);
                break;
            //pathinfo|rewrite
            case 1:
            default:
                defined('__MODULE__') or define("__MODULE__", __WEB__ . '/' . MODULE);
                defined('__CONTROLLER__') or define("__CONTROLLER__", __MODULE__ . '/' . CONTROLLER);
                defined('__ACTION__') or define("__ACTION__", __CONTROLLER__ . '/' . ACTION);
                break;
        }

    }

    /**
     * 分析路由
     * @param string $query
     * @return mixed
     */
    static private function parseRoute($query)
    {
        $query = str_ireplace(C('HTML_SUFFIX'), '', trim($query, '/'));
        $route = C("ROUTE");
        if (!$route or !is_array($route)) return $query;
        foreach ($route as $k => $v) {
            //正则路由
            if (preg_match("@^/.*/[isUx]*$@i", $k)) {
                //如果匹配URL地址
                if (preg_match($k, $query)) {
                    //元子组替换
                    $v = str_replace('#', '\\', $v);
                    //匹配当前正则路由,url按正则替换
                    return preg_replace($k, $v, $query);
                }
                //下一个路由规则
                continue;
            }
            //非正则路由
            $search = array(
                '@(:year)@i',
                '@(:month)@i',
                '@(:day)@i',
                '@(:num)@i',
                '@(:any)@i',
                '@(:[a-z0-9]+\\\d)@i',
                '@(:[a-z0-9]+\\\w)@i',
                '@(:[a-z0-9]+)@i'
            );
            $replace = array(
                '\d{4}',
                '\d{1,2}',
                '\d{1,2}',
                '\d+',
                '.+',
                '\d+',
                '\w+',
                '([a-z0-9]+)'
            );
            //将:year等替换
            $base_preg = "@^" . preg_replace($search, $replace, $k) . "$@i";
            //不满足路由规则
            if (!preg_match($base_preg, $query)) {
                continue;
            }
            //满足路由，但不存在参数如:uid等
            if (!strstr($k, ":")) {
                return $v;
            }
            /**
             * user/:id=>user/1
             */
            $vars = "";
            preg_match('/[^:\sa-z0-9]/i', $k, $vars);
            //:id=>"index/index"
            if (isset($vars[0])) {
                //拆分路由获得:id
                $roles_ex = explode($vars[0], $k);
                //上例中拆分请求参数获得1
                $url_args = explode($vars[0], $query);
            } else {
                $roles_ex = array($k);
                $url_args = array($query);
            }
            //匹配路由规则
            $query = $v;
            foreach ($roles_ex as $m => $n) {
                if (!strstr($n, ":")) {
                    continue;
                }
                $_GET[str_replace(":", "", $n)] = $url_args[$m];
            }
            return $query;
        }
        return $query;
    }


    /**
     * 将URL按路由规则进行处理
     * U()函数等使用
     * @access public
     * @param  string $url url字符串不含__WEB__.'?|/'
     * @return string
     */
    static public function toUrl($url)
    {
        $route = C("route");
        /**
         * 未定义路由规则
         */
        if (!$route) {
            return $url;
        }
        foreach ($route as $routeKey => $routeVal) {
            $routeKey = trim($routeKey);
            //正则路由
            if (substr($routeKey, 0, 1) === '/') {
                $regGroup = array(); //识别正则路由中的原子组
                preg_match_all("@\(.*?\)@i", $routeKey, $regGroup, PREG_PATTERN_ORDER);
                //路由规则Value
                $searchRegExp = $routeVal;
                //将正则路由的Value中的值#1换成(\d+)等形式
                for ($i = 0, $total = count($regGroup[0]); $i < $total; $i++) {
                    $searchRegExp = str_replace('#' . ($i + 1), $regGroup[0][$i], $searchRegExp);
                }
                //URL参数
                $urlArgs = array();
                //当前URL是否满足本次路由规则，如果满意获得url参数（原子组）
                preg_match_all("@^" . $searchRegExp . "$@i", $url, $urlArgs, PREG_SET_ORDER);
                //满足路由规则
                if ($urlArgs) {
                    //清除路由中的/$与/正则边界
                    $routeUrl = trim(preg_replace(array('@/\^@', '@/[isUx]$@', '@\$@'), array('', '', ''), $routeKey), '/');
                    /**
                     * 将路由规则中的(\d+)等形式替换为url中的具体值
                     * /admin(\d).html/   => admin1.html
                     */
                    foreach ($regGroup[0] as $k => $v) {
                        $v = preg_replace('@([\*\$\(\)\+\?\[\]\{\}\\\])@', '\\\$1', $v);
                        $routeUrl = preg_replace('@' . $v . '@', $urlArgs[0][$k + 1], $routeUrl, $count = 1);
                    }
                    return trim($routeUrl, '/');
                }
            } else {
                //获得如 "info/:city_:row" 中的:city与:row
                $routeGetVars = array();
                //普通路由处理
                //获得路由规则中以:开始的变量
                preg_match_all('/:([a-z]*)/i', $routeKey, $routeGetVars, PREG_PATTERN_ORDER);
                $getRouteUrl = $routeVal;
                switch (C("URL_TYPE")) {
                    case 1:
                        $getRouteUrl .= '/';
                        foreach ($routeGetVars[1] as $getK => $getV) {
                            $getRouteUrl .= $getV . '/(.*)/';
                        }
                        $getRouteUrl = '@' . trim($getRouteUrl, '/') . '@i';
                        break;
                    case 2:
                        $getRouteUrl .= '&';
                        foreach ($routeGetVars[1] as $getK => $getV) {
                            $getRouteUrl .= $getV . '=(.*)' . '&';
                        }
                        $getRouteUrl = '@' . trim($getRouteUrl, '&') . '@i';
                        break;
                }
                $getArgs = array();
                preg_match_all($getRouteUrl, $url, $getArgs, PREG_SET_ORDER);
                if ($getArgs) {
                    //去除路由中的传参数如:uid
                    $newUrl = $routeKey;
                    foreach ($routeGetVars[0] as $rk => $getName) {
                        $newUrl = str_replace($getName, $getArgs[0][$rk + 1], $newUrl);
                    }
                    return $newUrl;
                }
            }
        }
        return $url;
    }

    /**
     * 移除URL中的指定GET变量
     * 使用函数remove_url_param()调用
     * @param string $var 要移除的GET变量
     * @param null $url url地址
     * @return mixed|string 移除GET变量后的URL地址
     */
    static public function removeUrlParam($var, $url = null)
    {
        if (is_null($url)) {
            $url = __URL__;
        }
        $url = C('URL_TYPE') == 2 ? $url . '&' : $url . C("PATHINFO_DLI");
        switch (C('URL_TYPE')) {
            case 2: //普通模式
                $url = preg_replace(array("/$var=.*?&/", "/&&/"), '', $url);
                break;
            default: //pathinfo与兼容模式
                $url = preg_replace(array("/{$var}{$dli}.*?{$dli}/"), '', $url);
        }
        return rtrim($url, "&" . $dli);
    }

    /**
     * 根据配置文件的URL参数重新生成URL地址
     * @param String $path 访问url
     * @param array $args GET参数
     * <code>
     * $args = "nid=2&cid=1"
     * $args=array("nid"=>2,"cid"=>1)
     * </code>
     * @return string
     */
    static public function getUrl($path, $args = array())
    {
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }
        /**
         * 开启伪静态时去除入口文件
         */
        $root = C('URL_REWRITE') ? preg_replace('/\/?[a-z]+\.php/i', '', __WEB__) : __WEB__;
        /**
         * Host主机
         */
        switch (C("URL_TYPE")) {
            case 1:
                $root .= '/'; //入口位置
                break;
            case 2:
                $root .= C('URL_REWRITE') ? '/' : '?';
                break;
            case 3:
                $root .= (C('URL_REWRITE') ? '/' : '?') . C('PATHINFO_VAR') . '=';
                break;
        }
        if (preg_match('@[^\w/]@i', $path)) {
            /**
             * 参数如: m=Index&c=Index&a=index&cid=1&page=2
             * 这样形式时直接进行路由解析
             * 比如在分页类时使用
             */
            return $root . Route::toUrl($path) . C('HTML_SUFFIX');
        } else {
            $action = array();
            $info = explode('/', $path);
            if (count($info) > 3) {
                $param = array_slice($info, 3);
                $info = array_slice($info, 0, 3);
                for ($i = 0; $i < count($param); $i += 2) {
                    $args[$param[$i]] = $param[$i + 1];
                }
            }
            switch (count($info)) {
                case 3:
                    $action[C("VAR_MODULE")] = ucfirst($info[0]);
                    $action[C("VAR_CONTROLLER")] = ucfirst($info[1]);
                    $action[C("VAR_ACTION")] = $info[2];
                    break;
                case 2:
                    $action[C("VAR_MODULE")] = ucfirst(MODULE);
                    $action[C("VAR_CONTROLLER")] = ucfirst($info[0]);
                    $action[C("VAR_ACTION")] = $info[1];
                    break;
                case 1:
                    $action[C("VAR_MODULE")] = ucfirst(MODULE);
                    $action[C("VAR_CONTROLLER")] = ucfirst(CONTROLLER);
                    $action[C("VAR_ACTION")] = $info[0];
                    break;
                default:

            }
            switch (C("URL_TYPE")) {
                case 1:
                case 3:
                    $url = $action[C("VAR_MODULE")] . '/' . $action[C("VAR_CONTROLLER")] . '/' . $action[C("VAR_ACTION")];
                    break;
                case 2:
                    $url = C("VAR_MODULE") . '=' . $action[C("VAR_MODULE")] . '&' . C("VAR_CONTROLLER") . '=' . $action[C("VAR_CONTROLLER")] . '&' .
                        C("VAR_ACTION") . '=' . $action[C("VAR_ACTION")];
                    break;
            }
            /**
             * 参数$args为字符串时转数组
             */
            if (is_string($args)) {
                parse_str($args, $args);
            }
            /**
             * 处理参数
             */
            if (!empty($args)) {
                switch (C("URL_TYPE")) {
                    case 1:
                    case 3:
                        foreach ($args as $name => $value) {
                            $url .= '/' . $name . '/' . $value;
                        }
                        break;
                    case 2:
                        foreach ($args as $name => $value) {
                            $url .= '&' . $name . '=' . $value;
                        }
                        break;
                }
            }
            return $root . Route::toUrl($url) . C('HTML_SUFFIX');
        }
    }
}