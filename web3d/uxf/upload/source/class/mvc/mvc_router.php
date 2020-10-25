<?php

/**
 * @brief URL处理类
 * @note
 */
class Mvc_Router {
    //原生的Url形式,指从index.php，比如index.php?controller=blog&action=read&id=100
    const URL_MODE_NATIVE = 1;
    const URL_MODE_PATHINFO = 2; //pathinfo格式的Url,指的是：/blog/read/id/100
    const URL_MODE_DIY = 3; //经过urlRoute后的Url,指的是:/blog-100.html
    const URL_MODE_URL_PATHINFO = 4; ////index.php/blog/read/id/100
    
    const URL_KEY_CTRL = 'mod';
    const URL_KEY_ACTION = 'action';
    const URL_KEY_MODULE = 'module';
    
    const DEFAULT_MODULE = 'common';
    const DEFAULT_CTRL = 'main';
    const DEFAULT_ACTION = 'index';

    /**
     * @static REWRITE_RULE_DEFAULT
     * 'url',  不开启伪静态，同'get'，
     * /PHPRouter/index.php?controller=order&action=show&id=4
     *
     * 'pathinfo',需要启用自定义路径方式
     * /PhpRouter/user/home
     * 
     * 'url-pathinfo',不隐藏入口文件的路径方式
     * /PHPRouter/index.php/order/show/id/4
     * 
     */
    const REWRITE_RULE_DEFAULT = 1;
    const URL_KEY_ANCHOR = "/#&"; //urlArray中表示锚点的索引
    const URL_KEY_INTERROGATION = "?"; // /site/abc/?callback=/site/login callback=/site/login部分在UrlArray里的key

    private static $_urlRoute = array(); //路由规则的缓存
    private static $_injectMod = '';
    
    private static function _getInfo($key, $df){
        $info = preg_replace("/[^0-9a-z_]/i", '', getgpc($key, 'G'));
        return ($info == null) ? $df : $info;
    }
    
    /**
     * 获取模块名
     * @return string
     */
    public static function getModule(){
        return self::_getInfo(self::URL_KEY_MODULE, self::DEFAULT_MODULE);
    }
    
    /**
     * 获取控制器名
     * @return string
     */
    public static function getContrller(){
        return self::_getInfo(self::URL_KEY_CTRL, self::DEFAULT_CTRL);
    }
    
    /**
     * 获取动作名
     * @return string
     */
    public static function getAction(){
        return self::_getInfo(self::URL_KEY_ACTION, self::DEFAULT_ACTION);
    }

    /**
     * @brief 将Url从Ux支持的一种Url格式转成另一个格式。
     * @param string $url 想转换的url
     * @param int $from URL_MODE_NATIVE或者.....
     * @param int $to URL_MODE_PATHINFO或者.....
     * @return string 如果转换失败则返回false
     */
    public static function convertUrl($url, $from, $to) {
        if ($from == $to) {
            return $url;
        }

        $urlArray = "";
        $fun_re = false;
        switch ($from) {
            case self::URL_MODE_NATIVE :
                $urlArray = self::queryStringToArray($url);
                break;
            case self::URL_MODE_PATHINFO :
                $urlArray = self::pathinfoToArray($url);
                break;
            case self::URL_MODE_DIY :
                $urlArray = self::diyToArray($url);
                break;
            default:
                return $fun_re;
                break;
        }

        switch ($to) {
            case self::URL_MODE_NATIVE :
                $fun_re = self::_urlArrayToNative($urlArray);
                break;
            case self::URL_MODE_PATHINFO :
                $fun_re = self::_urlArrayToPathinfo($urlArray);
                break;
            case self::URL_MODE_DIY:
                $fun_re = self::_urlArrayToDiy($urlArray);
                break;
        }
        return $fun_re;
    }

    /**
     * @brief 将controller=blog&action=read&id=100类的query转成数组的形式
     * @param string $url
     * @return array
     */
    public static function queryStringToArray($url) {
        if (!is_array($url)) {
            $url = parse_url($url);
        }
        $query = isset($url['query']) ? explode("&", $url['query']) : array();
        $re = array();
        foreach ($query as $value) {
            $tmp = explode("=", $value);
            if (count($tmp) == 2) {
                $re[$tmp[0]] = $tmp[1];
            }
        }
        $re = self::_sortUrlArray($re);
        isset($url['fragment']) && ($re[self::URL_KEY_ANCHOR] = $url['fragment'] );
        return $re;
    }

    /**
     * @brief 将/blog/read/id/100形式的url转成数组的形式
     * @param string $url
     * @return array
     */
    public static function pathinfoToArray($url) {
        //blog/read/id/100
        //blog/read/id/100?comment=true#abcde
        $data = array();
        preg_match("!^(.*?)?(\\?[^#]*?)?(#.*)?$!", $url, $data);
        $re = array();
        if (isset($data[1]) && trim($data[1], "/ ")) {
            $string = explode("/", trim($data[1], "/ "));
            $key = null;
            $i = 1;
            //前两个是ctrl和action，后面的是参数名和值
            //2012.12.11 加入注入控制器的逻辑
            foreach ($string as $value) {
                if (self::$_injectMod) {
                    if ($i == 1) {
                        $tmpKey = self::URL_KEY_ACTION;
                        $re[$tmpKey] = $value;
                        $i ++;
                        continue;
                    }
                } else {
                    if($i == 1){
                        $tmp = explode('-', $value);
                        if (count($tmp) == 2) {
                            $re[self::URL_KEY_MODULE] = $tmp[0];
                            $re[self::URL_KEY_CTRL] = $tmp[1];
                        } else {
                            $re[self::URL_KEY_CTRL] = $value;
                        }
                        $i++;
                        continue;
                    } elseif ($i == 2) {
                        $tmpKey = self::URL_KEY_ACTION;
                        $re[$tmpKey] = $value;
                        $i++;
                        continue;
                    }

                }

                if ($key === null) {
                    $key = $value;
                    $re[$key] = "";
                } else {
                    $re[$key] = $value;
                    $key = null;
                }
            }
        }
        if (self::$_injectMod) {
            $re[self::URL_KEY_CTRL] = self::$_injectMod;
        }
        if (isset($data[2]) || isset($data[3])) {
            $re[self::URL_KEY_INTERROGATION] = ltrim($data[2], "?");
        }

        if (isset($data[3])) {
            $re[self::URL_KEY_ANCHOR] = ltrim($data[3], "#");
        }

        $re = self::_sortUrlArray($re);
        return $re;
    }

    /**
     * @brief 将用户请求的url进行路由转换，得到urlArray
     * @param string  $url
     * @return array
     */
    public static function diyToArray($url) {
        return self::_decodeRouteUrl($url);
    }

    /**
     * @brief 对Url数组里的数据进行排序
     * ctrl和action最靠前，其余的按key排序
     * @param array $re
     * @access private
     */
    private static function _sortUrlArray($re) {
        $fun_re = array();
        $fun_keys = array(self::URL_KEY_MODULE, self::URL_KEY_CTRL, self::URL_KEY_ACTION);
        foreach ($fun_keys as $value){
            if(array_key_exists($value, $re)){
                $fun_re[$value] = $re[$value];
                unset($re[$value]);
            }
        }
        ksort($re);
        $fun_re = array_merge($fun_re, $re);
        return $fun_re;
    }

    /**
     * @brief 将urlArray用pathinfo的形式表示出来
     * @access private
     */
    private static function _urlArrayToPathinfo($arr) {
        $re = "";
        $ctrl = isset($arr[self::URL_KEY_CTRL]) ? $arr[self::URL_KEY_CTRL] : '';
        $action = isset($arr[self::URL_KEY_ACTION]) ? $arr[self::URL_KEY_ACTION] : '';

        $ctrl != "" && ($re.="/{$ctrl}");
        $action != "" && ($re.="/{$action}");

        $fragment = isset($arr[self::URL_KEY_ANCHOR]) ? $arr[self::URL_KEY_ANCHOR] : "";
        $questionMark = isset($arr[self::URL_KEY_INTERROGATION]) ? $arr[self::URL_KEY_INTERROGATION] : "";
        unset($arr[self::URL_KEY_CTRL], $arr[self::URL_KEY_ACTION], $arr[self::URL_KEY_ANCHOR]);
        foreach ($arr as $key => $value) {
            $re.="/{$key}/{$value}";
        }
        if ($questionMark != "") {
            $re .= "?" . $questionMark;
        }
        $fragment != "" && ($re .= "#{$fragment}");
        return $re;
    }

    /**
     * @brief 将urlArray用原生url形式表现出来
     * @access private
     */
    private static function _urlArrayToNative($arr) {
        $re = "/";
        $re .= self::getIndexFile();
        $fragment = isset($arr[self::URL_KEY_ANCHOR]) ? $arr[self::URL_KEY_ANCHOR] : "";

        $question_mark = isset($arr[self::URL_KEY_INTERROGATION]) ? $arr[self::URL_KEY_INTERROGATION] : "";

        unset($arr[self::URL_KEY_ANCHOR], $arr[self::URL_KEY_INTERROGATION]);
        if (count($arr)) {
            $tmp = array();
            foreach ($arr as $key => $value) {
                $tmp[] = "{$key}={$value}";
            }
            $tmp = implode("&", $tmp);
            $re .= "?{$tmp}";
        }
        if (count($arr) && $question_mark != "") {
            $re .= "&" . $question_mark;
        } elseif ($question_mark != "") {
            $re .= "?" . $question_mark;
        }

        if ($fragment != "") {
            $re .= "#{$fragment}";
        }
        return $re;
    }

    /**
     * @brief 获取路由缓存
     * @return array
     */
    private static function _getRouteCache() {
        //配置文件中不存在路由规则
        if (self::$_urlRoute === false) {
            return null;
        }

        //存在路由的缓存信息
        if (self::$_urlRoute) {
            return self::$_urlRoute;
        }
    }

    /**
     * @brief 将urlArray转成路由后的url
     * @access private
     */
    private static function _urlArrayToDiy($arr) {
        if (!isset($arr[self::URL_KEY_CTRL]) || !isset($arr[self::URL_KEY_ACTION]) || !($route_list = self::_getRouteCache())) {
            return false;
        }
        
        //要合并m和c
        if(!empty($arr[self::URL_KEY_MODULE])){
            $arr[self::URL_KEY_CTRL] = $arr[self::URL_KEY_MODULE] . '-' . $arr[self::URL_KEY_CTRL];
        }

        foreach ($route_list as $level => $arr_reg) {
            foreach ($arr_reg as $reg_pattern => $value) {
                $urlArray = explode('/', trim($value, '/'), 3);

                if ($level == 0 && ($arr[self::URL_KEY_CTRL] . '/' . $arr[self::URL_KEY_ACTION] != $urlArray[0] . '/' . $urlArray[1])) {
                    continue;
                } else if ($level == 1 && ($arr[self::URL_KEY_ACTION] != $urlArray[1])) {
                    continue;
                } else if ($level == 2 && ($arr[self::URL_KEY_CTRL] != $urlArray[0])) {
                    continue;
                }

                $url = self::_parseRegPattern($arr, array($reg_pattern => $value));

                if ($url) {
                    return $url;
                }
            }
        }
        return false;
    }

    /**
     * @brief 根据规则生成URL
     * @param $urlArray array url信息数组
     * @param $reg_pattern array 路由规则
     * @return string or false
     */
    private static function _parseRegPattern($urlArray, $regArray) {
        $reg_pattern = key($regArray);
        $value = current($regArray);

        //存在自定义正则式
        if (preg_match_all("%<\w+?:.*?>%", $reg_pattern, $custom_reg_match)) {
            $reg_info = array();
            foreach ($custom_reg_match[0] as $val) {
                $val = trim($val, '<>');
                $regTemp = explode(':', $val, 2);
                $reg_info[$regTemp[0]] = $regTemp[1];
            }

            //匹配表达式参数
            $arr_replace = array();
            foreach ($reg_info as $key => $val) {
                if (strpos($val, '%') !== false) {
                    $val = str_replace('%', '\%', $val);
                }

                if (isset($urlArray[$key]) && preg_match("%$val%", $urlArray[$key])) {
                    $arr_replace[] = $urlArray[$key];
                    unset($urlArray[$key]);
                } else {
                    return false;
                }
            }

            $url = str_replace($custom_reg_match[0], $arr_replace, $reg_pattern);
        } else {
            $url = $reg_pattern;
        }

        //处理多余参数
        $arr_param = self::pathinfoToArray($value);

        $question_mark_key = isset($urlArray[self::URL_KEY_INTERROGATION]) ? $urlArray[self::URL_KEY_INTERROGATION] : '';
        $anchor = isset($urlArray[self::URL_KEY_ANCHOR]) ? $urlArray[self::URL_KEY_ANCHOR] : '';
        unset($urlArray[self::URL_KEY_CTRL], $urlArray[self::URL_KEY_ACTION], $urlArray[self::URL_KEY_ANCHOR], $urlArray[self::URL_KEY_INTERROGATION]);
        foreach ($urlArray as $key => $rs) {
            if (!isset($arr_param[$key])) {
                $question_mark_key .= '&' . $key . '=' . $rs;
            }
        }
        $url .= ($question_mark_key) ? '?' . trim($question_mark_key, '&') : '';
        $url .= ($anchor) ? '#' . $anchor : '';

        return $url;
    }

    /**
     * @brief 将请求的url通过路由规则解析成urlArray
     * @param $url string 要解析的url地址
     */
    private static function _decodeRouteUrl($url) {
        $url = trim($url, '/');
        $arr_url = array(); //url的数组形式
        $route_list = self::_getRouteCache();
        if (!$route_list) {
            return $arr_url;
        }

        foreach ($route_list as $level => $arr_reg) {
            foreach ($arr_reg as $reg_pattern => $value) {
                //解析执行规则的url地址
                $exeUrlArray = explode('/', $value);

                //判断当前url是否符合某条路由规则,并且提取url参数
                $reg_replace = preg_replace("%<\w+?:(.*?)>%", "($1)", $reg_pattern);
                if (strpos($reg_replace, '%') !== false) {
                    $reg_replace = str_replace('%', '\%', $reg_replace);
                }

                if (preg_match("%$reg_replace%", $url, $match_value)) {
                    //是否完全匹配整个完整url
                    $matchAll = array_shift($match_value);
                    if ($matchAll != $url) {
                        continue;
                    }

                    //如果url存在动态参数，则获取到$urlArray
                    if ($match_value) {
                        preg_match_all("%<\w+?:.*?>%", $reg_pattern, $matchReg);
                        foreach ($matchReg[0] as $key => $val) {
                            $val = trim($val, '<>');
                            $tempArray = explode(':', $val, 2);
                            $arr_url[$tempArray[0]] = isset($match_value[$key]) ? $match_value[$key] : '';
                        }

                        if (self::$_injectMod)
                            $arr_url[self::URL_KEY_CTRL] = self::$_injectMod;

                        //检测controller和action的有效性
                        if ((isset($arr_url[self::URL_KEY_CTRL]) && !preg_match("%^\w+$%", $arr_url[self::URL_KEY_CTRL]) ) || (isset($arr_url[self::URL_KEY_ACTION]) && !preg_match("%^\w+$%", $arr_url[self::URL_KEY_ACTION]) )) {
                            $arr_url = array();
                            continue;
                        }

                        //对执行规则中的模糊变量进行赋值
                        foreach ($exeUrlArray as $key => $val) {
                            $paramName = trim($val, '<>');
                            if (($val != $paramName) && isset($arr_url[$paramName])) {
                                $exeUrlArray[$key] = $arr_url[$paramName];
                            }
                        }
                    }

                    //分配执行规则中指定的参数
                    $paramArray = self::pathinfoToArray(join('/', $exeUrlArray));
                    $arr_url = array_merge($arr_url, $paramArray);
                    return $arr_url;
                }
            }
        }
        return $arr_url;
    }
    
    private static function _realUri(){
        $types = array('nginx', 'apache', 'iis');
        $server = 'apache';
        foreach($types as $type){
            if(stripos($_SERVER['SERVER_SOFTWARE'], $type) !== false ){
                $server = $type;
                break;
            }
        }
        switch ($server) {
            case 'nginx':
                $re = "";
		if(isset($_SERVER['DOCUMENT_URI']) )
		{
			$re = $_SERVER['DOCUMENT_URI'];
		}elseif( isset($_SERVER['REQUEST_URI']) ){
			$re = $_SERVER['REQUEST_URI'];
		}
		return $re;
                break;

            case 'iis':
                $re= "";
		if( isset($_SERVER['HTTP_X_REWRITE_URL'])  )
		{
			$re = isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['HTTP_X_REWRITE_URL'];
		}
		elseif(isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != "" )
		{
			$re = $_SERVER['PATH_INFO'];
		}
		elseif(isset($_SERVER["SCRIPT_NAME"] ) && isset($_SERVER['QUERY_STRING']) )
		{
			$re = $_SERVER["SCRIPT_NAME"] .'?'. $_SERVER['QUERY_STRING'];
		}
		return $re;
                break;
            default:
                return $_SERVER['PHP_SELF'];
                break;
        }
    }

    public static function tidy($url) {
        return preg_replace("![/\\\\]{2,}!", "/", $url);
    }

    /**
     * @brief  接收基准格式的URL，将其转换为Config中设置的模式
     * @param  String $url      传入的url
     * @return String $finalUrl url地址
     */
    public static function creatUrl($url = '', $scriptDir = '/') {
        if (preg_match("!^[a-z]+://!i", $url)) {
            return $url;
        }

        $baseUrl = self::getPhpSelf();
        if ($url == "") {
            return self::getScriptDir();
        } elseif ($url == "/") {
            return self::getScriptDir() . $baseUrl;
        }

        $rewriteRule = defined('REWRITE_RULE') ? REWRITE_RULE : self::URL_MODE_NATIVE;

        //判断是否需要返回绝对路径的url
        $baseDir = self::getScriptDir();//!$scriptDir ?  : $scriptDir;
        $baseUrl = self::tidy($baseUrl);
        $url = self::tidy($url);
        $tmpUrl = false;

        if ($rewriteRule == self::URL_MODE_DIY) {
            $tmpUrl = self::convertUrl($url, self::URL_MODE_PATHINFO, self::URL_MODE_DIY);
        }

        if ($tmpUrl !== false) {
            $url = $tmpUrl;
        } else {
            switch ($rewriteRule) {
                //case 'url': // 兼容以前的
                case self::URL_MODE_NATIVE : //config文件里叫get
                    $url = self::convertUrl($url, self::URL_MODE_PATHINFO, self::URL_MODE_NATIVE);
                    break;
                case self::URL_MODE_URL_PATHINFO:
                    $url = "/" . self::getIndexFile() . $url;
                    break;
            }
        }
        $url = self::tidy($baseDir . $url);
        return $url;
    }

    /**
     * @brief 获取网站根路径
     * @param  string $protocol 协议  默认为http协议，不需要带'://'
     * @return String $baseUrl  网站根路径
     *
     */
    public static function getHost($protocol = 'http') {
        $port = $_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT'];
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $baseUrl = $protocol . '://' . $host . $port;
        return $baseUrl;
    }

    /**
     * @brief 获取网站根路径
     * @param  string $protocol 协议  默认为http协议，不需要带'://'
     * @return String $baseUrl  网站根路径
     *
     */
    public static function getHostName() {
        $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        return $host;
    }

    /**
     * @brief 获取当前执行文件名
     * @return String 文件名
     */
    public static function getPhpSelf() {
        $re = explode("/", $_SERVER['SCRIPT_NAME']);
        return end($re);
    }

    /**
     * @brief 返回入口文件URl地址
     * @return string 返回入口文件URl地址
     */
    public static function getEntryUrl() {
        return self::getHost() . $_SERVER['SCRIPT_NAME'];
    }

    /**
     * @brief 获取入口文件名
     */
    public static function getIndexFile() {
        //return 'index.php';
        if(!isset($_SERVER['SCRIPT_NAME']))
          {
          return 'index.php';
          }
          else
          {
          return basename($_SERVER['SCRIPT_NAME']);
          }
    }

    /**
     * @brief 返回页面的前一页路由地址
     * @return string 返回页面的前一页路由地址
     */
    public static function getRefRoute() {
        if (isset($_SERVER['HTTP_REFERER']) && (self::getEntryUrl() & $_SERVER['HTTP_REFERER']) == self::getEntryUrl()) {
            return substr($_SERVER['HTTP_REFERER'], strlen(self::getEntryUrl()));
        } else
            return '';
    }

    /**
     * @brief  获取当前脚本所在文件夹
     * @return 脚本所在文件夹
     */
    public static function getScriptDir() {
        $re = trim(dirname($_SERVER['SCRIPT_NAME']), '\\');
        if ($re != '/') {
            $re = $re . "/";
        }
        return $re;
    }

    /**
     * @brief 获取当前url地址[经过RewriteRule之后的]
     * @return String 当前url地址
     */
    public static function getUrl() {
        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            // check this first so IIS will catch
            $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['IIS_WasUrlRewritten']) && $_SERVER['IIS_WasUrlRewritten'] == '1' && isset($_SERVER['UNENCODED_URL']) && $_SERVER['UNENCODED_URL'] != '') {
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            $requestUri = $_SERVER['UNENCODED_URL'];
        } elseif (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], "Apache") !== false) {
            $requestUri = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            // IIS 5.0, PHP as CGI
            $requestUri = $_SERVER['ORIG_PATH_INFO'];
            if (!empty($_SERVER['QUERY_STRING'])) {
                $requestUri .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            die("getUrl is error");
        }
        return self::getHost() . $requestUri;
    }

    /**
     * @brief 获取当前URI地址
     * @return String 当前URI地址
     */
    public static function getUri() {
        if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == "") {
            // IIS 的两种重写
            if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) {
                $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_ORIGINAL_URL'];
            } else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
                $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
            } else {
                //修正pathinfo
                if (!isset($_SERVER['PATH_INFO']) && isset($_SERVER['ORIG_PATH_INFO']))
                    $_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];


                if (isset($_SERVER['PATH_INFO'])) {
                    if ($_SERVER['PATH_INFO'] == $_SERVER['SCRIPT_NAME'])
                        $_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];
                    else
                        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
                }

                //修正query
                if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
                }
            }
        }
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @brief 获取url参数
     * //四种
        //native： /index.php?controller=blog&action=read&id=100
        //pathinfo:/blog/read/id/100
        //native-pathinfo:/index.php/blog/read/id/100
        //diy:/blog-100.html
     * @param String url 需要分析的url，默认为当前url
     */
    public static function beginUrl($url = '', $mod = '') {
        if ($mod && is_string($mod))
            self::$_injectMod = $mod;

        $url = !empty($url) ? $url : self::_realUri();
        // URL后缀
        define('__EXT__', strtolower(pathinfo($url,PATHINFO_EXTENSION)));

        preg_match('/\.php(.*)/', $url, $phpurl);

        if (!isset($phpurl[1]) || !$phpurl[1]) {
            if ($url != "") {
                //强行赋值 todo：检测是否有bug
                $phpurl = array(1 => "?");
            } else {
                return;
            }
        }
        $url = $phpurl[1];
        $urlArray = array();
        $rewriteRule = defined('REWRITE_RULE') ? REWRITE_RULE : self::URL_MODE_NATIVE;

        if ($rewriteRule !== self::URL_MODE_NATIVE) {
            $urlArray = self::_decodeRouteUrl($url); //将diy路由进行规则反解
        }

        if ($urlArray == array()) {
            if ($url[0] == '?') {
                $urlArray = $_GET;
            } else {
                $urlArray = self::pathinfoToArray($url);
            }
        }

        foreach ($urlArray as $key => $value) {
            setgpc($key, $value);
        }
    }
    
    /**
     * 当设置URL模式为DIY后，需要先调用此方法设置diy规则
     * array(
     *  'diy_route' => '<controller>---<action>'
     * );
     * @param array $rules
     */
    public static function setRouteRules($rules){
        //存在路由的缓存信息
        if (self::$_urlRoute) {
            return self::$_urlRoute;
        }
        
        if(!is_array($rules))
            return null;

        $cacheRoute = array();
        foreach ($rules as $key => $val) {
            if (is_array($val)) {
                continue;
            }

            $tempArray = explode('/', trim($val, '/'), 3);
            if (count($tempArray) < 2) {
                continue;
            }
            
            //进行路由规则的级别划分,$level越低表示匹配优先
            $level = 3;
            if (($tempArray[0] != '<' . self::URL_KEY_CTRL . '>') && ($tempArray[1] != '<' . self::URL_KEY_ACTION . '>'))
            $level = 0;
            elseif (($tempArray[0] == '<' . self::URL_KEY_CTRL . '>') && ($tempArray[1] != '<' . self::URL_KEY_ACTION . '>'))
                $level = 1;
            elseif (($tempArray[0] != '<' . self::URL_KEY_CTRL . '>') && ($tempArray[1] == '<' . self::URL_KEY_ACTION . '>'))
                $level = 2;

            $cacheRoute[$level][$key] = $val;
        }

        if (empty($cacheRoute)) {
            self::$_urlRoute = false;
            return null;
        }

        ksort($cacheRoute);
        self::$_urlRoute = $cacheRoute;
    }

    /**
     * @brief  获取拼接两个地址
     * @param  String $path_a
     * @param  String $path_b
     * @return string 处理后的URL地址
     */
    public static function getRelative($path_a, $path_b) {
        $path_a = strtolower(str_replace('\\', '/', $path_a));
        $path_b = strtolower(str_replace('\\', '/', $path_b));
        $arr_a = explode("/", $path_a);
        $arr_b = explode("/", $path_b);
        $i = 0;
        while (true) {
            if ($arr_a[$i] == $arr_b[$i])
                $i++;
            else
                break;
        }
        $len_b = count($arr_b);
        $len_a = count($arr_a);
        if (!$arr_b[$len_b - 1])
            $len_b = $len_b - 1;
        if (!$len_a[$len_a - 1])
            $len_a = $len_a - 1;
        $len = ($len_b > $len_a) ? $len_b : $len_a;
        $str_a = '';
        $str_b = '';
        for ($j = $i; $j < $len; $j++) {
            if (isset($arr_a[$j])) {
                $str_a .= $arr_a[$j] . '/';
            }
            if (isset($arr_b[$j]))
                $str_b .= "../";
        }
        return $str_b . $str_a;
    }

}
