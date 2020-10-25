<?php

if (defined('QEE_VER')) return;

# 定义错误反馈类型
error_reporting(E_ALL | E_STRICT);

require_once __DIR__ . '/Autoload.php';

/**
 * DIRECTORY_SEPARATOR 的简写
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

/**
 * QeePHP 框架基本库所在路径
 */
define('QEE_PATH', rtrim(__DIR__, '/\\') . DS);
define('PACKAGES_PATH', dirname(__DIR__));

/**
 * 确定 QeePHP 是否使用调试模式
 */
defined('QEE_DEBUG') or define('QEE_DEBUG', false);

/**
 * 定义 QeePHP 版本号
 */
define('QEE_VER', 'qeephp-3.0');

/**
 * 定义当前时间戳来减少对 time() 函数的调用
 */
define('CURRENT_TIMESTAMP', time());

/**
 * 定义程序的执行模式
 */
define('APP_IN_CLI', PHP_SAPI === 'cli');

if (APP_IN_CLI)
{
    for ($i = 1; $i < $_SERVER['argc']; $i++)
    {
        $arg = explode('=', $_SERVER['argv'][$i]);
        if (count($arg) > 1 || strncmp($arg[0], '-', 1) === 0)
        {
            $_GET[ltrim($arg[0], '-')] = isset($arg[1]) ? $arg[1] : true;
        }
        $_REQUEST = array_merge($_REQUEST,$_GET);
    }
}

/**
 * 返回应用程序对象
 *
 * @return \qeephp\mvc\App
 */
function app()
{
    return \qeephp\mvc\App::instance();
}

/**
 * 对字符串或数组进行格式化，返回格式化后的数组
 *
 * $input 参数如果是字符串，则首先以“,”为分隔符，将字符串转换为一个数组。
 * 接下来对数组中每一个项目使用 trim() 方法去掉首尾的空白字符。最后过滤掉空字符串项目。
 *
 * 该方法的主要用途是将诸如：“item1, item2, item3” 这样的字符串转换为数组。
 *
 * @code php
 * $input = 'item1, item2, item3';
 * $output = arr($input);
 * // $output 现在是一个数组，结果如下：
 * // $output = array(
 * //   'item1',
 * //   'item2',
 * //   'item3',
 * // );
 * @endcode
 *
 * 可以通过 $delimiter 参数指定使用什么字符来分割：
 *
 * @code php
 * $input = 'item1|item2|item3';
 * // 指定使用“|”字符作为分割符
 * $output = arr($input, '|');
 * @endcode
 *
 * @param array|string $input 要格式化的字符串或数组
 * @param string $delimiter 按照什么字符进行分割
 *
 * @return array 格式化结果
 */
function arr($input, $delimiter = ',')
{
    if (!is_array($input))
    {
        $input = explode($delimiter, $input);
    }
    $input = array_map('trim', $input);
    return array_filter($input, 'strlen');
}

/**
 * 构造 URL 地址
 *
 * @param string $action_name 动作名
 * @param array|string $params 要添加到 URL 中的附加参数
 * @param string $anchor 锚点
 *
 * @return string 构造好的 URL 地址
 */
function url($action_name, $params=null, $anchor=null)
{
    return \qeephp\mvc\App::instance()->url($action_name, $params, $anchor);
}

/**
 * 转换 HTML 特殊字符，等同于 htmlspecialchars()
 *
 * @param string $text
 *
 * @return string
 */
function h($text)
{
    return htmlspecialchars($text);
}

/**
 * 输出转义后的字符串
 *
 * @param string $text
 */
function p($text)
{
    echo htmlspecialchars($text);
}

function t($text)
{
    return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($text)));
}

function t2js($content)
{
    return str_replace(array("\r", "\n"), array('', '\n'), addslashes($content));
}

function val($arr, $name, $default = null)
{
    return isset($arr[$name]) ? $arr[$name] : $default;
}

function request($name, $default = null)
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function get($name, $default = null)
{
    return isset($_GET[$name]) ? $_GET[$name] : $default;
}

function post($name, $default = null)
{
    return isset($_POST[$name]) ? $_POST[$name] : $default;
}

function cookie($name, $default = null)
{
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

function session($name, $default = null)
{
    return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
}

function server($name, $default = null)
{
    return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
}

function env($name, $default = null)
{
    return isset($_ENV[$name]) ? $_ENV[$name] : $default;
}

/**
 * 取得请求的 URI 信息（不含协议、主机名）
 *
 * 例如：
 *
 * http://sfken.xu/admin/index.php?controller=test
 *
 * 返回：
 *
 * /admin/index.php?controller=test
 *
 * @return string
 */
function get_request_uri()
{
    static $request_uri = null;
    if (!is_null($request_uri)) return $request_uri;

    if (isset($_SERVER['HTTP_X_REWRITE_URL']))
    {
        $request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
    }
    elseif (isset($_SERVER['REQUEST_URI']))
    {
        $request_uri = $_SERVER['REQUEST_URI'];
    }
    elseif (isset($_SERVER['ORIG_PATH_INFO']))
    {
        $request_uri = $_SERVER['ORIG_PATH_INFO'];
        if (!empty($_SERVER['QUERY_STRING']))
        {
            $request_uri .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    else
    {
        $request_uri = '';
    }

    return $request_uri;
}

/**
 * 取得请求的 URI 信息（不含协议、主机名、查询参数、PATHINFO）
 *
 * 例如：
 *
 * http://sfken.xu/admin/index.php?controller=test
 * http://sfken.xu/admin/index.php/path/to
 *
 * 都返回：
 *
 * /admin/index.php
 *
 * @return string
 */
function get_request_baseuri()
{
    static $request_base_uri = null;
    if (!is_null($request_base_uri)) return $request_base_uri;

    $filename = basename($_SERVER['SCRIPT_FILENAME']);

    if (basename($_SERVER['SCRIPT_NAME']) === $filename)
    {
        $url = $_SERVER['SCRIPT_NAME'];
    }
    elseif (basename($_SERVER['PHP_SELF']) === $filename)
    {
        $url = $_SERVER['PHP_SELF'];
    }
    elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename)
    {
        $url = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
    }
    else
    {
        // Backtrack up the script_filename to find the portion matching
        // php_self
        $path = $_SERVER['PHP_SELF'];
        $segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
        $segs = array_reverse($segs);
        $index = 0;
        $last = count($segs);
        $url = '';
        do
        {
            $seg = $segs[$index];
            $url = '/' . $seg . $url;
            ++ $index;
        } while (($last > $index) && (false !== ($pos = strpos($path, $url))) && (0 != $pos));
    }

    // Does the baseUrl have anything in common with the request_uri?
    $request_uri = get_request_uri();

    if (0 === strpos($request_uri, $url))
    {
        // full $url matches
        $request_base_uri = $url;
        return $request_base_uri;
    }

    if (0 === strpos($request_uri, dirname($url)))
    {
        // directory portion of $url matches
        $request_base_uri = rtrim(dirname($url), '/') . '/';
        return $request_base_uri;
    }

    if (! strpos($request_uri, basename($url)))
    {
        // no match whatsoever; set it blank
        $request_base_uri = '';
        return '';
    }

    // If using mod_rewrite or ISAPI_Rewrite strip the script filename
    // out of baseUrl. $pos !== 0 makes sure it is not matching a value
    // from PATH_INFO or QUERY_STRING
    if ((strlen($request_uri) >= strlen($url))
        && ((false !== ($pos = strpos($request_uri, $url)))
        && ($pos !== 0)))
    {
        $url = substr($request_uri, 0, $pos + strlen($url));
    }

    $request_base_uri = rtrim($url, '/') . '/';
    return $request_base_uri;
}

/**
 * 取得响应请求的 .php 文件在 URL 中的目录部分
 *
 * 例如：
 *
 * http://sfken.xu/admin/index.php?controller=test
 *
 * 返回：
 *
 * /admin/
 *
 * @return string
 */
function get_request_dir()
{
    static $dir = null;
    
    $base_uri = get_request_baseuri();
    if (substr($base_uri, - 1, 1) == '/')
    {
        $dir = $base_uri;
    }
    else
    {
        $dir = dirname($base_uri);
    }

    $dir = rtrim($dir, '/\\') . '/';
    return $dir;    
}

/**
 * 返回 PATHINFO 信息
 *
 * 例如：
 *
 * http://sfken.xu/admin/index.php/path/to
 *
 * 返回：
 *
 * /path/to
 *
 * @return string
 */
function get_request_pathinfo()
{
    static $pathinfo = null;
    if (!is_null($pathinfo)) return $pathinfo;
    
    if (!empty($_SERVER['PATH_INFO'])) 
    {
        $pathinfo = $_SERVER['PATH_INFO'];
        return $pathinfo;
    }

    $base_url = get_request_baseuri();

    if (null === ($request_uri = get_request_uri())) return '';

    // Remove the query string from REQUEST_URI
    if (($pos = strpos($request_uri, '?')))
    {
        $request_uri = substr($request_uri, 0, $pos);
    }

    if ((null !== $base_url) && (false === ($pathinfo = substr($request_uri, strlen($base_url)))))
    {
        // If substr() returns false then PATH_INFO is set to an empty string
        $pathinfo = '';
    }
    elseif (null === $base_url)
    {
        $pathinfo = $request_uri;
    }
    return $pathinfo;
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_ajax()
{
    return strtolower(get_http_header('X_REQUESTED_WITH')) == 'xmlhttprequest';
}

function is_flash()
{
    return strtolower(get_http_header('USER_AGENT')) == 'shockwave flash';
}

function get_http_header($header)
{
    $name = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
    return server($name, '');
}

/**
 * fast_uuid 为模型生成 64 位整数或混淆字符串的不重复 ID
 * 
 * 参数 suffix_len指定 生成的 ID 值附加多少位随机数，默认值为 3
 * 
 * @param int suffix_len
 * 
 * @return string
 */
function fast_uuid($suffix_len=3){
    //! 计算种子数的开始时间
    static $being_timestamp = 1336681180;// 2012-5-10
        
    $time = explode(' ', microtime());
    $id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
    if ($suffix_len > 0)
    {
        $id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
    }
    return $id;
}

/**
 * qeephp\debug\Debug::dump() 的简写，用于输出一个变量的内容
 *
 * @param mixed $vars 要输出的变量
 * @param string $label 输出变量时显示的标签
 * @param int $depth
 * @param bool $return
 *
 * @return string
 */
function dump($vars, $label = null, $depth = null, $return = false)
{
    if ( !QEE_DEBUG ) return;
    if ($return) ob_start();
    \qeephp\debug\Debug::dump($vars, $label, $depth);
    if ($return) return ob_get_clean();
}

/**
 * print_r 函数的美化，用于输出一个变量的内容
 *
 * @param mixed $vars 要输出的变量
 * @param string $label 输出变量时显示的标签
 * @param bool $return
 *
 * @return string
 */
function prety_printr($vars, $label = '', $return = false)
{
    $content = "<pre>\n";
    if ($label != '') {
        $content .= "<strong>{$label} :</strong>\n";
    }
    $content .= htmlspecialchars(print_r($vars, true),ENT_COMPAT | ENT_IGNORE);
    $content .= "\n</pre>\n";

    if ($return) { return $content; }
    echo $content;
}

/**
 * 重定向浏览器到指定的 URL
 * 
 * @param string $url
 * @param int $delay
 */
function redirect($url, $delay =0)
{
    $delay = (int) $delay;
    if (headers_sent() || $delay > 0)
    {
        $out = '<html>
        <head>
        <meta http-equiv="refresh" content="%d;URL=%s" />
        </head>
        </html>';
        echo sprintf($out, $url, $delay);
    }
    else
    {
        header("Location: {$url}");
    }       
    exit;
}

/**
 * safe_file_put_contents() 一次性完成打开文件，写入内容，关闭文件三项工作，并且确保写入时不会造成并发冲突
 *
 * @param string $filename
 * @param string $content
 *
 * @return boolean
 */
function safe_file_put_contents($filename, & $content)
{
    $fp = fopen($filename, 'w');
    if (!$fp) { return false; }
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return false;
    }
    fwrite($fp, $content);
    fclose($fp);
    return true;
}

/**
 * 遍历指定目录及子目录下的文件，返回所有与匹配模式符合的文件名
 *
 * @param string $dir
 * @param string $pattern
 *
 * @return array
 */
function recursion_glob($dir, $pattern)
{
    $dir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
    $files = array();

    $dh = opendir($dir);
    if (!$dh) return $files;

    $items = (array)glob($dir . $pattern);
    foreach ($items as $item)
    {
        if (is_file($item)) $files[] = $item;
    }

    while (($file = readdir($dh)))
    {
        if ($file == '.' || $file == '..') continue;

        $path = $dir . $file;
        if (is_dir($path))
        {
            $files = array_merge($files, recursion_glob($path, $pattern));
        }
    }
    closedir($dh);
    return $files;
}

/**
 * 从一个二维数组中返回指定键的所有值
 *
 * @param array $arr
 * @param string $col
 *
 * @return array
 */
function array_col_values(array &$arr, $col)
{
    $ret = array();
    foreach ($arr as $row) {
        if (isset($row[$col])) { $ret[] = $row[$col]; }
    }
    return $ret;
}

/**
 * 将一个二维数组转换为 hashmap
 *
 * 如果省略 $valueField 参数，则转换结果每一项为包含该项所有数据的数组。
 *
 * @param array $arr
 * @param string $keyField
 * @param string $valueField
 *
 * @return array
 */
function array_to_hashmap(array &$arr, $keyField, $valueField = null)
{
    $ret = array();
    if ($valueField) {
        foreach ($arr as $row) {
            $ret[$row[$keyField]] = $row[$valueField];
        }
    } else {
        foreach ($arr as $row) {
            $ret[$row[$keyField]] = $row;
        }
    }
    return $ret;
}

/**
 * 将一个二维数组按照指定字段的值分组
 *
 * @param array $arr
 * @param string $keyField
 *
 * @return array
 */
function array_group_by(array &$arr, $keyField)
{
    $ret = array();
    foreach ($arr as $row) {
        $key = $row[$keyField];
        $ret[$key][] = $row;
    }
    return $ret;
}

/**
 * 根据指定的键对数组排序
 *
 * 用法：
 * @code php
 * $rows = array(
 *     array('id' => 1, 'value' => '1-1', 'parent' => 1),
 *     array('id' => 2, 'value' => '2-1', 'parent' => 1),
 *     array('id' => 3, 'value' => '3-1', 'parent' => 1),
 *     array('id' => 4, 'value' => '4-1', 'parent' => 2),
 *     array('id' => 5, 'value' => '5-1', 'parent' => 2),
 *     array('id' => 6, 'value' => '6-1', 'parent' => 3),
 * );
 *
 * $rows = sort_by_col($rows, 'id', SORT_DESC);
 * dump($rows);
 * // 输出结果为：
 * // array(
 * //   array('id' => 6, 'value' => '6-1', 'parent' => 3),
 * //   array('id' => 5, 'value' => '5-1', 'parent' => 2),
 * //   array('id' => 4, 'value' => '4-1', 'parent' => 2),
 * //   array('id' => 3, 'value' => '3-1', 'parent' => 1),
 * //   array('id' => 2, 'value' => '2-1', 'parent' => 1),
 * //   array('id' => 1, 'value' => '1-1', 'parent' => 1),
 * // )
 * @endcode
 *
 * @param array $array 要排序的数组
 * @param string $keyname 排序的键
 * @param int $dir 排序方向
 *
 * @return array 排序后的数组
 */
function sort_by_col(array $array, $keyname, $dir = SORT_ASC)
{
    return sort_by_multicols($array, array($keyname => $dir));
}

/**
 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
 *
 * 用法：
 * @code php
 * $rows = sort_by_multicols($rows, array(
 *     'parent' => SORT_ASC,
 *     'name' => SORT_DESC,
 * ));
 * @endcode
 *
 * @param array $rowset 要排序的数组
 * @param array $args 排序的键
 *
 * @return array 排序后的数组
 */
function sort_by_multicols(array $rowset, array $args)
{
    $sortArray = array();
    $sortRule = '';
    foreach ($args as $sortField => $sortDir)
    {
        foreach ($rowset as $offset => $row)
        {
            $sortArray[$sortField][$offset] = $row[$sortField];
        }
        $sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
    }
    if (empty($sortArray) || empty($sortRule)) { return $rowset; }
    eval('array_multisort(' . $sortRule . '$rowset);');
    return $rowset;
}

/**
 * 将一个平面的二维数组按照指定的字段转换为树状结构
 *
 * 用法：
 * @code php
 * $rows = array(
 *     array('id' => 1, 'value' => '1-1', 'parent' => 0),
 *     array('id' => 2, 'value' => '2-1', 'parent' => 0),
 *     array('id' => 3, 'value' => '3-1', 'parent' => 0),
 *
 *     array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
 *     array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
 *     array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
 *     array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
 * );
 *
 * $tree = array_to_tree($rows, 'id', 'parent', 'nodes');
 *
 * dump($tree);
 *   // 输出结果为：
 *   // array(
 *   //   array('id' => 1, ..., 'nodes' => array()),
 *   //   array('id' => 2, ..., 'nodes' => array(
 *   //        array(..., 'parent' => 2, 'nodes' => array()),
 *   //        array(..., 'parent' => 2, 'nodes' => array()),
 *   //   ),
 *   //   array('id' => 3, ..., 'nodes' => array(
 *   //        array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
 *   //             array(..., , 'parent' => 9, 'nodes' => array(),
 *   //        ),
 *   //   ),
 *   // )
 * @endcode
 *
 * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
 * @code php
 * $refs = null;
 * $tree = array_to_tree($rows, 'id', 'parent', 'nodes', $refs);
 *
 * // 输出 id 为 3 的节点及其所有子节点
 * $id = 3;
 * dump($refs[$id]);
 * @endcode
 *
 * @param array $arr 数据源
 * @param string $key_node_id 节点ID字段名
 * @param string $key_parent_id 节点父ID字段名
 * @param string $key_children 保存子节点的字段名
 * @param boolean $refs 是否在返回结果中包含节点引用
 *
 * return array 树形结构的数组
 */
function array_to_tree($arr, $key_node_id, $key_parent_id = 'parent_id',
                       $key_children = 'children', & $refs = null)
{
    $refs = array();
    foreach ($arr as $offset => $row)
    {
        $arr[$offset][$key_children] = array();
        $refs[$row[$key_node_id]] =& $arr[$offset];
    }

    $tree = array();
    foreach ($arr as $offset => $row)
    {
        $parent_id = $row[$key_parent_id];
        if ($parent_id)
        {
            if (!isset($refs[$parent_id]))
            {
                $tree[] =& $arr[$offset];
                continue;
            }
            $parent =& $refs[$parent_id];
            $parent[$key_children][] =& $arr[$offset];
        }
        else
        {
            $tree[] =& $arr[$offset];
        }
    }

    return $tree;
}

/**
 * 将树形数组展开为平面的数组
 *
 * 这个方法是 array_to_tree() 方法的逆向操作。
 *
 * @param array $tree 树形数组
 * @param string $key_children 包含子节点的键名
 *
 * @return array 展开后的数组
 */
function tree_to_array(array $tree, $key_children = 'children')
{
    $ret = array();
    if (isset($tree[$key_children]) && is_array($tree[$key_children]))
    {
        $children = $tree[$key_children];
        unset($tree[$key_children]);
        $ret[] = $tree;
        foreach ($children as $node)
        {
            $ret = array_merge($ret, tree_to_array($node, $key_children));
        }
    }
    else
    {
        unset($tree[$key_children]);
        $ret[] = $tree;
    }
    return $ret;
}

/**
 * 获取两日期差值数组
 *
 * 边界 限定符:
 *
 * () 不包含边界
 * [] 包含边界
 * (] 不包含左边界
 * [) 不包含右边界
 *
 * @param string $start_create_date
 * @param string $end_create_date
 * @param string $inclusive 是否包含边界
 *
 * @return array
 */
function date_diff_array($start_create_date ,$end_create_date ,$inclusive='[]'){
    static $day_interval = 86400;
    $data = array();
    $start = (int) strtotime($start_create_date);
    $end = (int) strtotime($end_create_date);

    if ($start > $end) return $data;

    for ($start += $day_interval;$start < $end;){
        $data[] = date('Y-m-d',$start);
        $start += $day_interval;
    }

    switch ($inclusive){
        case '()':
            break;
        case '[]':
            array_unshift($data,$start_create_date);
            if ($start_create_date != $end_create_date)
                $data[] = $end_create_date;
            break;
        case '(]':
            $data[] = $end_create_date;
            break;
        case '[)':
            array_unshift($data,$start_create_date);
            break;
        default:
            break;
    }

    return $data;
}

/**
 * 序列化对象
 * PHP 自带的 serialize和unserialize 函数存在一些缺陷
 *
 * 当数组值包含如双引号、单引号或冒号等字符时，它们被反序列化后，可能会出现问题
 * 为了克服这个问题，一个巧妙的技巧是使用base64_encode和base64_decode
 * 但是base64编码将增加字符串的长度。为了克服这个问题，可以和gzcompress一起使用
 *
 * 序列化也可以使用 json_encode 和 json_decode ,但是它不能序列化对象,
 * 而 serialize()和unserialize()会自动调用对象的魔法方法__sleep()和__wakeup()
 * 在 序列化对象时有更好的兼容性
 *
 * @param mixed $obj
 *
 * @return string
 */
function obj_serialize($obj){
    return base64_encode(gzcompress(serialize($obj)));
}

/**
 * 反序列化
 *
 * @param string $txt
 *
 * @return mixed
 */
function obj_unserialize($txt){
    return unserialize(gzuncompress(base64_decode($txt)));
}

/**
 * 获取 远程访问者的IP地址
 *
 * @return string
 */
function real_ip_addr()
{
    static $remote_ip_address = null;
    if (!$remote_ip_address){
        $vars = array(
            val($_SERVER,'HTTP_CLIENT_IP'),
            val($_SERVER,'HTTP_X_FORWARDED_FOR'),
            val($_SERVER,'REMOTE_ADDR'),
        );
        foreach ($vars as $var){
            if (!empty($var)) {
                $remote_ip_address = $var;
            }
            continue;
        }
        if (empty($remote_ip_address)) $remote_ip_address = 'unknown';
    }
    return $remote_ip_address;
}

/**
 * 向浏览器发送文件内容
 *
 * @param string $serverPath 文件在服务器上的路径（绝对或者相对路径）
 * @param string $filename 发送给浏览器的文件名（尽可能不要使用中文）
 * @param string $charset 指示内容字符集
 * @param string $mimeType 指示文件类型
 */
function send_file($serverPath, $filename,$charset = 'UTF-8', $mimeType = 'application/octet-stream')
{
    $attachmentHeader = content_disposition_header($_SERVER["HTTP_USER_AGENT"],$filename,'attachment',$charset);
    $filesize = filesize($serverPath);

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: {$mimeType}");

    header($attachmentHeader);
    header('Pragma: cache');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header("Content-Length: {$filesize}");
    readfile($serverPath);
    exit;
}

/**
 * 生成 HTTP 响应头信息中的 Content-Disposition: {Content-Disposition}; filename
 * 解决 其它浏览器下 中文文件名称乱码 的问题
 *
 * @param string $httpUserAgent
 * @param string $filename
 * @param string $contentDisposition 缺省是 attachment
 * @param string $charset 缺省是 UTF-8
 *
 * @return string
 */
function content_disposition_header($ua,$filename,$contentDisposition='attachment',$charset = 'UTF-8'){
    // 文件名乱码问题
    if (preg_match("/MSIE/", $ua)) {
        $filename = urlencode($filename);
        $filename = str_replace("+", "%20", $filename);// 替换空格
        $attachmentHeader = "Content-Disposition: {$contentDisposition}; filename=\"{$filename}\"; charset={$charset}";
    } else if (preg_match("/Firefox/", $ua)) {
        $attachmentHeader = 'Content-Disposition: '.$contentDisposition.'; filename*="utf8\'\'' . $filename. '"' ;
    } else {
        $attachmentHeader = "Content-Disposition: '.$contentDisposition.'; filename=\"{$filename}\"; charset={$charset}";
    }

    return $attachmentHeader;
}

