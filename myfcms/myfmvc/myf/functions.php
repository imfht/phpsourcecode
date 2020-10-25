<?php

/*
 *  @author myf
 *  @date 2014-11-13 
 *  @Description  基本函数类库
 *  @web http://www.minyifei.cn
 */

/**
 * 对象输出
 * @param type $var
 * @param type $echo
 * @param type $label
 * @param type $strict
 * @return string|null
 */
function dump($var, $echo = true, $label = null, $strict = true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else {
        return $output;
    }
}

/**
 * 获取客户端IP
 * @staticvar null $ip
 * @return null
 */
function getClientIP() {
    static $ip = NULL;
    if ($ip !== NULL) {
        return $ip;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 * 获取当前时间毫秒数
 */
function getMillisecond() {
    list($s1, $s2) = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
}

function getCurrentTime(){
    return date("Y-m-d H:i:s");
}

/**
 * 循环创建文件夹
 */
function createFolders($dir) {
    return is_dir($dir) or ( createFolders(dirname($dir)) and mkdir($dir, 0777));
}

/**
 * GET请求
 * @param String $name 变量
 * @param Object $default 默认值
 * @return type
 */
function get($name,$default=null) {
    if(isset($_GET[$name])){
        $value = $_GET[$name];
    }else{
        $value = $default;
    }
    return $value;
}

/**
 * 读取config文件
 * @param String $name
 */
function C($name = null) {
    global $_config;
    if (!strpos($name, '.')) {
        return isset($_config[$name]) ? $_config[$name] : null;
    }
    // 二维数组设置和获取支持
    $name = explode('.', $name);
    return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
}

/**
 * 读取POST值
 * @param String $name 变量
 * @param type $default 默认值
 * @return type
 */
function post($name,$default=null) {
    if(isset($_POST[$name])){
        $value = $_POST[$name];
        return $value;
    }else{
        return $default;
    }
}

/**
 * 获取纯字符串
 * @param type $name
 * @return null
 */
function getUrlString($name) {
    $value = filter_input(INPUT_GET, $name, FILTER_SANITIZE_URL);
    if ($value) {
        return $value;
    } else {
        return null;
    }
}

/**
 * 获取Integer变量
 * @param String $name
 * @return NULL|number
 */
function getInteger($name) {
    if(isset($_REQUEST[$name])){
        $value = $_REQUEST[$name];
        if (!is_numeric($value)) {
            return null;
        } else {
            return intval($value);
        }
    }else{
        return null;
    }
}

function getDouble($name) {
    if(isset($_REQUEST[$name])){
        $value = $_REQUEST[$name];
        if (!is_numeric($value)) {
            return null;
        } else {
            return (double)$value;
        }
    }else{
        return null;
    }
}

/**
 * 获取项目基础相对URL
 * @return string
 */
function getBasePath() {
    $sitePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($sitePath == "/" || $sitePath == "\\") {
        $sitePath = "";
    }
    return $sitePath;
}

function getBaseURL(){
    return getBasePath();
}

/**
 * 获取项目基础绝对URL
 * @return string
 */
function getFullURL() {
    $pageURL = 'http://';
    $sitePath = getBasePath();
    $host = $_SERVER["HTTP_HOST"];
    $port = $_SERVER["SERVER_PORT"];
    if ($port != "80") {
        $pageURL .= $host . $sitePath;
    } else {
        $pageURL .= str_replace(":80","",$host) . $sitePath;
    }
    return $pageURL;
}

/**
 * 获取项目相对URL路径
 * @return null
 */
function getProjectURL() {
    $sysPath = dirname(dirname(__FILE__));
    $cwd = getcwd();
    $filepath = str_replace($sysPath, "", $cwd);
    $url = str_replace($filepath, "", getFullURL());
    return $url;
}

/**
 * 自动加载类
 * @param String $className 类名
 * @throws Exception
 */
function loader($className) {
    $file = getClassFile($className);
    if (is_file($file)) {
        require_once($file);
    } else {
        throw new Exception($className . " not found");
    }
}

/**
 * 获取类绝对文件路径
 * @global type $namespaces
 * @param type $className
 * @return string
 */
function getClassFile($className) {
    global $namespaces;
    $names = explode("\\", $className);
    $class = array_pop($names);
    $key = join("\\", $names);
    if ($key == "Myf\Mvc") {
        $path = APP_SYS_PATH;
    } else {
        $path = $namespaces[$key];
    }
    $file = $path . "/" . $class . ".php";
    return $file;
}

/**
 * 获取类的文件名
 * @global type $namespaces
 * @param type $className
 * @return type
 */
function getClassFileName($className) {
    global $namespaces;
    $names = explode("\\", $className);
    $class = array_pop($names);
    return $class;
}

/**
 * 驼峰命名转下划线命名，如 UserName => user_name
 * @param type $s
 * @return string
 */
function toUnderLineName($s) {
    $s = lcfirst($s);
    $chars = str_split($s);
    $res = "";
    foreach ($chars as $c) {
        if (isCapitalLetter($c)) {
            $c = "_" . strtolower($c);
        }
        $res.=$c;
    }
    return $res;
}

/**
 * 判断字符串是否为大写字母
 * @param type $c
 * @return boolean
 */
function isCapitalLetter($c) {
    if (preg_match('/^[A-Z]+$/', $c)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 对象转数组
 * @param type $obj
 * @return type
 */
function objectToArray($obj) {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    foreach ($_arr as $key => $val) {
        $val = (is_array($val)) || is_object($val) ? objectToArray($val) : $val;
        $arr[$key] = $val;
    }
    return $arr;
}

/**
 * mvc路由配置
 * @return Array
 */
function getMvcRoute() {
    //url rewrite 读取路由
    $s = getUrlString("s");
    //控制器
    $c = getUrlString("c");
    if (empty($c)) {
        $c = "index";
    }
    $a = getUrlString("a");
    if (empty($a)) {
        $a = "index";
    }
    //默认控制器
    $route = array(
        "c" => $c,
        "a" => $a,
    );
    if (!empty($s)) {
        $s = trim(str_replace("/", " ", strtolower($s)));
        $urls = explode(" ", $s);
        if (isset($urls[0])) {
            $route["c"] = $urls[0];
        }
        if (isset($urls[1])) {
            $route["a"] = $urls[1];
        }
    }
    return $route;
}

// session管理函数
function session($name, $value = '') {
    $prefix = "myfcms_";
    if ('' === $value) {
        if (0 === strpos($name, '[')) {// session 操作
            if ('[pause]' == $name) {// 暂停session
                session_write_close();
            } elseif ('[start]' == $name) {// 启动session
                session_start();
            } elseif ('[destroy]' == $name) {// 销毁session
                $_SESSION = array();
                session_unset();
                session_destroy();
            } elseif ('[regenerate]' == $name) {// 重新生成id
                session_regenerate_id();
            }
        } elseif (0 === strpos($name, '?')) {// 检查session
            $name = substr($name, 1);
            if ($prefix) {
                return isset($_SESSION[$prefix][$name]);
            } else {
                return isset($_SESSION[$name]);
            }
        } elseif (is_null($name)) {// 清空session
            if ($prefix) {
                unset($_SESSION[$prefix]);
            } else {
                $_SESSION = array();
            }
        } elseif ($prefix) {// 获取session
            return $_SESSION[$prefix][$name];
        } else {
            return $_SESSION[$name];
        }
    } elseif (is_null($value)) {// 删除session
        if ($prefix) {
            unset($_SESSION[$prefix][$name]);
        } else {
            unset($_SESSION[$name]);
        }
    } else {// 设置session
        if ($prefix) {
            if (!is_array($_SESSION[$prefix])) {
                $_SESSION[$prefix] = array();
            }
            $_SESSION[$prefix][$name] = $value;
        } else {
            $_SESSION[$name] = $value;
        }
    }
}

// Cookie 设置、获取、删除
function cookie($name, $value = '', $option = null) {
    // 默认设置
    $config = array('prefix' => "myf", // cookie 名称前缀
        'expire' => '36000', // cookie 保存时间
        'path' => '.', // cookie 保存路径
        'domain' => null, // cookie 有效域名
    );
    // 参数设置(会覆盖黙认设置)
    if (!empty($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config = array_merge($config, array_change_key_case($option));
    }
    // 清除指定前缀的所有cookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return;
        // 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return;
    }
    $name = $config['prefix'] . $name;
    if ('' === $value) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
        // 获取指定Cookie
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
            unset($_COOKIE[$name]);
            // 删除指定cookie
        } else {
            // 设置cookie
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain']);
            $_COOKIE[$name] = $value;
        }
    }
}

/**
 * 解析url的参数
 * @param String $query
 * @return Array 解析后返回key-value对象
 */
function parseUrlParams($query){
    $queryParts = explode('&', $query); 
    $params = array(); 
    foreach ($queryParts as $param) 
    { 
        $item = explode('=', $param); 
        $params[$item[0]] = $item[1]; 
    } 
    return $params; 
}

/**
 * 字符串加密
 * @param type $original
 * @return type
 */
function encodePassword($original){
    $encoder = md5(base64_encode($original."_myfcms"));
    return $encoder;
}

/**
 * 制作tree
 */
function makeTree($data,$pid=0,$pname="parentId"){
    $tree = array();
    foreach ($data as $value) {
        if($value[$pname]==$pid){
            $value["childs"]=makeTree($data,$value["id"],$pname);	
            $tree[]=$value;
        }
    }
    return $tree;
}

/**
 * 查找指定元素的所有父类元素
 */
function parentTrees($data,$id,$pname="parentId"){
    $tree = array();
    foreach ($data as $value) {
        if($value["id"]==$id){
            if($value[$pname]>0){
                $tree=parentTrees($data, $value[$pname],$pname);
            }
            $tree[]=$value;
        }
    }
    return $tree;
}

function childTree($data,$pid=0,$pname="pid"){
    $tree = array();
    foreach ($data as $value) {
        if($value[$pname]==$pid){
            $value["childs"]=childTree($data, $value["id"]);
            $tree[]=$value;
        }
    }
    return $tree;
}

function formatArcUrl($typedir,$pubtime,$aid,$arcnamerule){
    $ptime = strtotime($pubtime);
    //当前年份
    $year = date("Y", $ptime);
    //当前月份
    $month = date("m", $ptime);
    //当前日期
    $day = date("d", $ptime);
    $searchArr = array("{aid}","{Y}","{M}","{D}","{typedir}");
    $replaceArr = array($aid,$year,$month,$day,$typedir);
    $filename = str_replace($searchArr, $replaceArr, $arcnamerule);
    return $filename;
}

function getParamsValue($params,$key){
    if(isset($params[$key])){
        return $params[$key];
    }else{
        return null;
    }
}