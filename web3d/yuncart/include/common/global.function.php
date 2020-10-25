<?php

defined('IN_CART') or die;

/**
 *
 * 打印变量
 * 
 */
function dump($val)
{
    echo "<pre>";
    if (is_string($val) || is_numeric($val))
        echo $val;
    elseif (is_array($val)) {
        print_r($val);
    } elseif (is_object($val)) {
        print_r($val);
    }
    echo "</pre>";
}

/**
 *
 * 数组转化为字符串
 * 
 */
function cimplode($str)
{
    if (!is_array($str))
        return "(" . trim($str, ",") . ")";
    if ($str)
        return "(" . implode(",", $str) . ")";
    return "(0)";
}

/**
 *
 * 转换编码
 * 
 */
function charset($string, $in_charset, $out_charset)
{
    if (function_exists("mb_convert_encoding")) {
        return mb_convert_encoding($string, $out_charset, $in_charset);
    } else if (function_exists("iconv")) {
        return iconv($in_charset, $out_charset . "//IGNORE", $string);
    }
    return $string;
}

/**
 *
 * safehtml
 * 
 */
function safehtml($html)
{
    if (is_array($html)) {
        foreach ($html as $key => $val) {
            $html[$key] = safehtml($val);
        }
    } else {
        $html = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), htmlspecialchars($html));
    }
    return $html;
}

/**
 *
 * 计算字符长度
 * 
 */
function getClength($str)
{
    return (strlen($str) + mb_strlen($str, "utf-8")) / 2;
}

/**
 *
 * 反斜线引用字符串
 * 
 */
function caddslashes($value)
{
    if (empty($value)) {
        return $value;
    } else {
        return is_array($value) ? array_map("caddslashes", $value) : addslashes($value);
    }
}

/**
 *
 * 判断是否email
 * 
 */
function isemail($email)
{
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 *
 * 设置cookie
 * 
 */
function csetcookie($name, $value, $life = 0)
{
    setcookie($GLOBALS['_CONF']['cookiepre'] . "_" . $name, $value, $life ? (time() + $life) : 0, $GLOBALS['_CONF']['cookiepath'], $GLOBALS['_CONF']['cookiedomain'], $_SERVER['SERVER_PORT'] == '443' ? 1 : 0);
}

/**
 *
 * 获取cookie
 * 
 */
function cgetcookie($name)
{
    return isset($_COOKIE[$GLOBALS['_CONF']['cookiepre'] . "_" . $name]) ? $_COOKIE[$GLOBALS['_CONF']['cookiepre'] . "_" . $name] : "";
}

/**
 *
 * 判断字符串中是否存在特定字符
 * 
 */
function cstrpos($haystack, $needle)
{
    return strpos(strval($haystack), strval($needle)) !== false;
}

/**
 *
 * 返回格式化后的价格
 *
 */
function getPrice($price, $n = -2, $return = 'string')
{

    $price = floatval($price) * pow(10, $n);

    if ($return == "string") {
        $price = sprintf("%.2f", round($price, 2));
    } else if ($return == "int") {
        $price = intval(round($price));
    } else if ($return == "float") {
        $price = round($price, 2);
    }
    return $price;
}

/**
 *
 * url重定向
 * 
 */
function url($pagetype, $model, $action = '', $other = '', $html = false)
{
    $concat = $html ? "&amp;" : "&";
    $url = ($pagetype == 'admin' ? getConfig('adminfile', 'admin') : $pagetype) . ".php?";
    $model && ($url .= "model=" . $model);
    $action && ($url .= $concat . "action=" . $action);
    $other && ($url .= $concat . $other);
    if ($pagetype == 'admin') {
        if ($model == 'login' && $action != 'logout')
            return $url;
        else
            return !empty($_SESSION['admin']['token']) ? "{$url}{$concat}token={$_SESSION['admin']['token']}" : $url;
    }
    return $url;
}

/**
 *
 * 重定向
 * 
 */
function redirect($url)
{
    $redirect = '';
    if (is_string($url)) {
        $redirect = $url;
    } elseif (is_array($url)) {
        $redirect = $url[0] . ".php";
        !empty($url[1]) && $redirect .= "?model=" . $url[1];
        !empty($url[2]) && $redirect .= "?action=" . $url[2];
        !empty($url[3]) && $redirect .= "&" . $other;
    }
    if (!$redirect) {
        global $stage;
        $redirect = ($stage == "admin") ? "admin.php" : "index.php";
    }
    header("HTTP/1.1 301 Moved Permanently");
    header("Location:$redirect");
    exit();
}

/**
 *
 * 生成路径，级联，考虑要生成index.html，不采用mkdir第三个参数级联
 * 
 */
function remkdir($dir, $mode = 0777)
{
    if (is_dir($dir)) {
        return true;
    }
    if (@mkdir($dir, $mode)) {
        @touch(trim($dir, "/") . "/index.html");
        return true;
    } else {
        $ddir = dirname($dir);
        remkdir($ddir, $mode);
    }
    if (@mkdir($dir, $mode)) {
        @touch(trim($dir, "/") . "/index.html");
        return true;
    }
    return false;
}

/**
 *
 * 返回默认值
 * 
 */
function def($value, $default = '')
{
    return (!empty($value) && $value) ? $value : $default;
}

/**
 *
 * 过滤path
 * 
 */
function bpath($path, $keyval)
{
    $path = "," . trim(strval($path), ",") . ",";
    $keyval = strval($keyval);
    list($key, $val) = explode(":", $keyval);
    $my = trim(preg_replace("/,$key(.+?),/", ',', $path), ",");
    if (!$val)
        return $my;
    return $my ? $my . "," . $keyval : $keyval;
}

/**
 *
 * 连接字符串
 * 
 */
function combine($str1, $str2 = '')
{
    return strval($str1) . strval($str2);
}

/**
 *
 * 获取客户端IP
 * 
 */
function getClientIp()
{
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}

/**
 *
 * 获取随机字符串
 * 
 */
function getRandString($length = 8, $strict = false)
{
    $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    if ($strict)
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ3456789";
    $len = strlen($str) - 1;
    $ret = "";
    for ($i = 1; $i <= $length; $i++) {
        $ret .= substr($str, mt_rand(0, $len), 1);
    }
    return $ret;
}

/**
 *
 * 获取分页所需参数，返回array
 * 
 */
function getPageArr($page, $pagesize, $count, $pageurl = '', $pageajax = false)
{
    $pagetotal = ceil($count / $pagesize);
    ($page < 1) && ($page = 1);
    ($page > $pagetotal) && ($page = $pagetotal);
    $pagearr = array("page" => $page,
        "pagesize" => $pagesize,
        "pagetotal" => $pagetotal,
        "limit" => ($page - 1) * $pagesize . "," . $pagesize,
        "count" => $count,
        "nextpage" => $page + 1 > $pagetotal ? $pagetotal : $page + 1,
        "pageurl" => $pageurl,
        "pageajax" => $pageajax
    );
    $pagearr += getPageInfo($page, $pagetotal);
    return $pagearr;
}

/**
 *
 * 获取分页信息
 * 
 */
function getPageInfo($page, $pagetotal)
{
    $from = $to = 0;
    $prev = $next = false;
    if ($page <= 4) {
        $prev = false;
        $next = true;
        $from = 1;
        $to = $from + 5;
        if ($to > $pagetotal) {
            $to = $pagetotal;
            $next = false;
        }
    } else if ($page + 2 >= $pagetotal) {
        $from = $pagetotal - 5;
        $to = $pagetotal;
        $prev = true;
        $next = false;
    }
    if (!$from) {
        $from = $page - 3;
        $to = $page + 2;
        $prev = $next = true;
    }
    return array("from" => $from, "to" => $to, "prev" => $prev, "next" => $next, "fromtoarr" => range($from, $to));
}

/**
 *
 * 加密密码
 *
 */
function encpass($pass = "")
{
    $salt = getRandString(4);
    $pass = md5($salt . $pass);
    return array("pass" => $pass, "salt" => $salt);
}

/**
 *
 * 检查密码是否正确
 *
 */
function checkpass($pass, $salt, $encrypted)
{
    return md5($salt . $pass) == $encrypted;
}

/**
 *
 * 语言替换
 *
 */
function __($key, $vals = array())
{
    global $_LANG;
    if (isset($_LANG[$key])) {
        $text = $_LANG[$key];
        if ($vals !== false) {
            if (is_array($vals)) {
                foreach ($vals as $k => $v) {
                    $text = str_replace("\\" . ($k + 1), $v, $text);
                }
            } else {
                $text = str_replace("\\1", $vals, $text);
            }
        }
        return $text;
    } else {
        return $key;
    }
}

function getMd5String($str, $length = 8)
{
    return substr(md5($str), 0, $length);
}

/**
 *
 * 格式化大小
 *
 */
function formatBytes($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
    } elseif ($bytes >= 1048576) {
        $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
    } elseif ($bytes >= 1024) {
        $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
    } else {
        $bytes = $bytes . 'Bytes';
    }
    return $bytes;
}

function jsonString($str)
{
    return preg_replace("/([\\\\\/'])/", '\\\$1', $str);
}

function ispostreq()
{
    return isset($_SERVER["REQUEST_METHOD"]) && strtoupper($_SERVER["REQUEST_METHOD"]) == "POST";
}

/**
 *
 * 数组转化为下拉菜单
 *
 */
function array2select($array, $valuekey = '', $textkey = '', $selected = 0)
{
    $html = "";
    if (empty($array))
        return $html;
    foreach ($array as $key => $val) {
        if (is_array($val)) {//二纬数组
            $html .= "<option value= " . $val[$valuekey] . "";
            if ($val[$valuekey] == $selected)
                $html .= " selected";
            $html .= ">" . $val[$textkey] . "</option>";
        } else { //一纬数组
            $html .= "<option value= " . $$valuekey . "";
            if ($$valuekey == $selected)
                $html .= " selected";
            $html .= ">" . $$textkey . "</option>";
        }
    }
    return $html;
}

/**
 *
 * 导出csv
 *
 */
function import($content, $filename = '')
{
    !$filename && ($filename = date('YmdHis') . '.csv');
    header("Content-Type:text/csv");
    header("Content-Disposition:attachment; filename=" . $filename);
    echo iconv("utf-8", "gb2312//ignore", $content);
}

function getDistrict($province = '', $city = '', $district = '')
{
    return Dis::getText($province, $city, $district);
}

/**
 *
 * 通过commoncache获取text
 *
 */
function getCommonCache($code, $type)
{
    static $source = array();
    $type = strtoupper($type);
    if (!$source) {
        $source = include CACHEDIR . '/commoncache.php';
    }
    if ($code == 'all')
        return $source[$type]; //全部数组

    if (cstrpos($code, ",")) {//如果含有,
        $ret = array();
        $codearr = explode(",", $code);
        foreach ($codearr as $key => $val) {
            $val = strtolower($val);
            if (!isset($source[$type][$val]))
                continue;
            $ret[] = $source[$type][$val];
        }
        return implode(",", $ret);
    } elseif (is_string($code)) {//单独一个code
        $code = strtolower($code);
        return isset($source[$type][$code]) ? $source[$type][$code] : "";
    }
    return "";
}

/**
 *
 * 显示网站错误
 *
 */
function cerror($error = '', $url = '')
{
    global $stage;
    if ($stage == 'admin') {//后台错误
        if (isset($_SESSION['adminerror'])) {//防止循环错误
            unset($_SESSION['adminerror']);
            halt($error);
        } else {
            $_SESSION['adminerror'] = $error;
            !$url && $url = url('admin', 'dashboard', 'index');
            redirect($url);
        }
    } else if ($stage == "front") {//前台错误
        if (isset($_SESSION['fronterror'])) {
            unset($_SESSION['fronterror']);
            halt($error);
        } else {
            $_SESSION['fronterror'] = $error;
            !$url && $url = url('index', 'front', 'hint');
            redirect($url);
        }
    }
}

/**
 *
 * 终止程序执行
 *
 */
function halt($error)
{
    $error = safehtml($error);
    echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Yuncart 提示</title>
</head>
<body>
	<div style="width:800px;margin:0 auto;padding-left:20px;height:600px;">
		<h1 style="height:36px;line-height:36px;padding-left:20px;font-weight:bold;font-size:14px;">提示</h1>
		<div style="width:800px;border:1px solid #ddd;margin:0 auto;margin-bottom:20px;text-align:center;padding:20px 0">$error</div>
	</div>
	<div style="text-align:center;">Copyright © 2012 版权所有 Powered By <a href="http://www.yuncart.com" target="_blank">yuncart</a></div>
</body>
</html>
END;
    exit();
}

/**
 *
 * 写log
 *
 */
function clog($file, $logdata)
{
    $logdir = DATADIR . "/logs";
    !is_dir($logdir) && mkdir($logdir, 0777) || @chmod($logdir, 0777);
    $logfile = $logdir . "/" . $file . ".php";

    $method = $_SERVER["REQUEST_METHOD"];
    $url = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME']);
    $logdata = "<?php exit() ?>" . "\t"
            . getClientIp() . "\t"
            . "[" . date("Y-m-d H:i:s") . "]\t"
            . "\"" . $method . " " . $url . "\"\t"
            . $logdata
            . CRLF
    ;

    if (@filesize($logfile) > 2048000) { //如果文件大
        $logfiles = glob($logdir . "/" . $file . "*.php");
        $count = count($logfiles);
        $logfile = $logdir . "/" . $file . "_" . $count . ".php";
        if (@filesize($logfile) > 2048000) {
            $logfile = $logdir . "/" . $file . "_" . ($count + 1) . ".php";
        }
    }
    if (@$fp = fopen($logfile, "ab")) {
        @flock($fp, LOCK_EX);
        fwrite($fp, $logdata);
        fclose($fp);
        return true;
    } else {
        //hint($logfile.__("not_writable"));
        return false;
    }
}

/**
 *
 * 写文件
 *
 */
function cwritefile($file, $data, $mode = "wb")
{
    if (!is_writable($file)) {
        $ch = @chmod($file, "0755");
    }
    if (@$fp = fopen($file, $mode)) {
        flock($fp, LOCK_EX);
        fwrite($fp, $data);
        fclose($fp);
        return true;
    } else {
        clog("error", "File：$file write error");
        return false;
    }
}

/**
 *
 * 读文件
 *
 */
function creadfile($file)
{
    $content = "";
    if (@$fp = fopen($file, "r")) {
        $content = fread($fp, filesize($file));
        fclose($fp);
    }
    return $content;
}

/**
 *
 * 删除目录
 *
 */
function deldir($dir)
{
    $dir = rtrim($dir, "//") . "/";
    $ret = @chmod($dir, 0777);
    $handler = @opendir($dir);
    if ($handler) {
        while (false !== ($file = @readdir($handler))) {
            if ($file != "." && $file != "..") {
                if (is_dir($dir . $file)) {
                    if (!deldir($dir . $file)) {
                        return false;
                    }
                } else {
                    @chmod($dir . $file, 0777);
                    if (!@unlink($dir . $file)) {
                        return false;
                    }
                }
            }
        }
        @closedir($handler);
        if (!@rmdir($dir)) {
            return false;
        }
        return true;
    }
    return false;
}

/**
 *
 * 设置权限
 *
 */
function setMod($dir)
{
    $dir = rtrim($dir, "//") . "/";
    $handler = @opendir($dir);
    if ($handler) {
        while (false !== ($file = @readdir($handler))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . $file)) {
                    setMod($dir . $file);
                }
                @chmod($dir . $file, 0777);
            }
        }
        @closedir($handler);
        $ret = @chmod($dir, 0777);
        return $ret;
    }
    return false;
}
