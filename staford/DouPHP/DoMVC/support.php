<?php

/*************************************************************
* 框架支持文件
* @abstract 框架的基础配置、基础操作、零散操作都在这文件
* @note 框架内部预定义函数使用驼峰法命名且首字母也大写
* @author 暮雨秋晨
* @copyright 2014
*************************************************************/

/**
 * @name LoadExt（扩展类加载函数）
 * @abstract 使用本函数，类文件及类名必须遵守相关命名规则。Filename:test.class.php Classname:Test
 * @param string $ClassName 目标名称
 * @return bool
 */
function LoadExt($class)
{
    $ClassName = strtolower($class);
    if (is_file($file = INC . DS . $ClassName . '.class.php')) {
        require_once $file;
        return true;
    } else {
        return false;
    }
}

/**
 * @abstract 各类型验证函数，为filter()函数的扩展函数
 */

//使用正则表达式进行验证（filter分支函数）
function Filter_RegExp($string, $regexp)
{
    if (filter_var($string, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" =>
                "{$regexp}")))) {
        return true;
    } else {
        return false;
    }
}
//邮箱验证函数（filter分支函数）
function Filter_Mail($str)
{
    if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}
//URL验证函数（Filter分支函数）
function Filter_Url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    } else {
        return false;
    }
}
//IP验证函数（Filter分支函数）
function Filter_Ip($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return true;
    } else {
        return false;
    }
}

/**
 * @abstract 数据转义
 */
function DataEscape($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $data[$key] = DataEscape($val);
            } else {
                $data[$key] = htmlspecialchars(addslashes($val), ENT_QUOTES);
            }
        }
    } else {
        $data = htmlspecialchars(addslashes($data), ENT_QUOTES);
    }
    return $data;
}

/**
 * @abstract 自动判断字符串编码并输出指定编码后的字符串
 * @param string $string 待检测、转换的字符串
 * @param string $outEncoding 输出编码，默认UTF-8
 */
function SafeEncoding($string, $outEncoding = 'UTF-8')
{
    $encoding = "UTF-8";
    for ($i = 0; $i < strlen($string); $i++) {
        if (ord($string{$i}) < 128)
            continue;

        if ((ord($string{$i}) & 224) == 224) {
            //第一个字节判断通过
            $char = $string{++$i};
            if ((ord($char) & 128) == 128) {
                //第二个字节判断通过
                $char = $string{++$i};
                if ((ord($char) & 128) == 128) {
                    $encoding = "UTF-8";
                    break;
                }
            }
        }
        if ((ord($string{$i}) & 192) == 192) {
            //第一个字节判断通过
            $char = $string{++$i};
            if ((ord($char) & 128) == 128) {
                // 第二个字节判断通过
                $encoding = "GB2312";
                break;
            }
        }
    }
    if (strtoupper($encoding) == strtoupper($outEncoding))
        return $string;
    else
        return iconv($encoding, $outEncoding, $string);
}

/**
 * utf-8编码下截取中文字符串,参数可以参照substr函数
 * @param $str 要进行截取的字符串
 * @param $start 要进行截取的开始位置，负数为反向截取
 * @param $end 要进行截取的长度
 */
function ZhCut($str, $start = 0, $end = 1)
{
    if (empty($str)) {
        return false;
    }
    if (function_exists('mb_substr')) {
        if (func_num_args() >= 3) {
            $end = func_get_arg(2);
            return mb_substr($str, $start, $end, 'utf-8');
        } else {
            mb_internal_encoding("UTF-8");
            return mb_substr($str, $start);
        }
    } else {
        $null = "";
        preg_match_all("/./u", $str, $ar);
        if (func_num_args() >= 3) {
            $end = func_get_arg(2);
            return join($null, array_slice($ar[0], $start, $end));
        } else {
            return join($null, array_slice($ar[0], $start));
        }
    }
}
//ZhCut()函数副本
function utf8_substr($str, $start = 0, $end = 1)
{
    return ZhCut($str, $start, $end);
}

/**
 * @abstract 变量信息打印函数
 */
function dump()
{
    $args = func_get_args();
    if (!empty($args)) {
        echo ("\r\n" . '<br />--[Debug start]--<br />' . "\r\n");
        foreach ($args as $arg) {
            if (is_array($arg)) {
                echo ('<pre>');
                print_r($arg);
                echo ('</pre>');
            } elseif (is_string($arg)) {
                echo ($arg);
            } else {
                var_dump($arg);
            }
        }
        echo ("\r\n" . '<br />--[Debug   end]--<br />' . "\r\n");
    }
}

/**
 * @abstract 异常捕获函数
 */
function exception_handler($e)
{
    die('Error：' . $e->getMessage());
}
set_exception_handler('exception_handler');

/**
 * @abstract 自动加载函数
 */
function autoload($name)
{
    $name = ucfirst(strtolower($name));
    if (is_file($file = DOU_CLASS_DIR . DS . $name . '.php')) {
        require_once $file;
    } else {
        return 0;
    }
}
spl_autoload_register('autoload'); //注册自动加载函数

/**
 * @abstract 设置项目模板
 */
Template::setTemplate(TEMPLATE);

/**
 * @abstract 判断、设置错误输出
 */
if (DEBUG) {
    ini_set('display_errors', 'On');
    error_reporting(8191);
} else {
    ini_set('display_errors', 'Off');
    error_reporting(0);
}

if (SESSION) {
    session_start();
}

/**
 * @abstract 重置系统全局变量
 */
$_REQUEST = $_GET + $_POST + $_COOKIE;
if (isset($_SERVER['PATH_INFO']) && $_SERVER['PHP_SELF'] === $_SERVER['SCRIPT_NAME'] .
    $_SERVER['PATH_INFO']) {
    $_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
}
ignore_user_abort(true); //打开脚本后台执行
set_time_limit(60); //设置超时时间60秒
if (get_magic_quotes_runtime()) {
    set_magic_quotes_runtime(0); //关闭系统自带转义
}

/**
 * @abstract 全局防注入
 */
if (isset($_GET) && !empty($_GET)) {
    $_GET = DataEscape($_GET);
}
if (isset($_POST) && !empty($_POST)) {
    $_POST = DataEscape($_POST);
}
if (isset($_COOKIE) && !empty($_COOKIE)) {
    $_COOKIE = DataEscape($_COOKIE);
}

header("Content-type: text/html; charset=" . OUTPUT_ENCODING); //输出HTTP头
iconv_set_encoding("internal_encoding", "UTF-8"); //内部编码
iconv_set_encoding("output_encoding", OUTPUT_ENCODING); //输出编码
ob_start("ob_iconv_handler"); //打开输出缓冲控制
