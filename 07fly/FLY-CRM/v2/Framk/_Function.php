<?php
/**
 * +------------------------------------------------------------------------------
 * Framk PHP框架
 * +------------------------------------------------------------------------------
 * @package  Framk
 * @author   shawn fon <shawn.fon@gmail.com>
 * +------------------------------------------------------------------------------
 */

date_default_timezone_set('PRC');
@header("content-Type: text/html; charset=utf-8 ");
if (substr(PHP_VERSION, 0, 1) < '5') _error('error', 'Framk框架运行环境要求PHP5以上,当前版本为：' . PHP_VERSION);

$GLOBALS = _config(require(FRAMK . '_Config.php'), require(CONFIG . 'Config.php'));//获取配置

//$GLOBALS["xmlconf"]=require(EXTEND . 'Xml.php');
$mtime = explode(' ', microtime());
$GLOBALS['StartRunTime'] = $mtime[1] + $mtime[0];
$GLOBALS['Debug'] ? error_reporting(E_ALL) : error_reporting(0);

if ($GLOBALS['Session']) session_start();
if (version_compare(PHP_VERSION, '5.0.0', '>')) @date_default_timezone_set('Asia/Shanghai');

define('NOWTIME', date('Y-m-d H:i:s', time()));
define('NOWDATE', date('Y-m-d ', time()));
//@set_magic_quotes_runtime(0);
//	foreach($_POST  as $id=>$v){
//		$_POST[$id]=common_htmlspecialchars($v);
//	}
//	foreach($_GET  as $id=>$v){
//		if(!is_array($v))
//		$v=substr(strip_tags($v),0,30);
//		$_GET[$id]=common_htmlspecialchars($v);
//	}
//	foreach($_COOKIE  as $id=>$v){
//		$v=substr(strip_tags($v),0,32);
//		$_COOKIE[$id]=common_htmlspecialchars($v);
//	}
//	function quotesGPC() {
//		$_POST	= array_map("addSlash", $_POST);
//		$_GET 	= array_map("addSlash", $_GET);
//		$_COOKIE= array_map("addSlash", $_COOKIE);
//	}
//	function addSlash($el) {
//		if (is_array($el))
//			return array_map("addSlash", $el);
//		else
//			return addslashes($el);
//	}
function common_htmlspecialchars($str)
{
    /*查找替换
                array('<', '>', '"','and',"'","insert","delete","update","select","%20","count","chr","truncate"),
                array('&lt;', '&gt;', '&quot;','an d',"”","Ｉnsert","Ｄelete","Ｕpdate","Ｓelect","","Ｃount","Ｃhr","Ｔruncate")

    */

    //$str = preg_replace('/&(?!#[0-9]+;)/s', '&amp;', $str);
    /*$str = str_replace(
                array('and',"insert","delete","update","select","%20","count","chr","truncate"),
                array('an d',"Ｉnsert","Ｄelete","Ｕpdate","Ｓelect","","Ｃount","Ｃhr","Ｔruncate"),
            $str);*/
    return $str;
}


/*
获取Config.php
*/
function _config($_Config, $userConfig)
{
    $config = array();
    $configArray = array_merge($_Config, $userConfig);//合并系统配置与用户配置，将用户配置覆盖系统配置
    foreach ($configArray as $key => $value) {
        $config[$key] = $value;
    }
    return $config;
}

/* 
建立多级目录，如果存在则返回目录，否则循环建立多级目录再返回目录
*/
function _mkdir($dir, $mode = 0777)
{

    if (is_dir($dir)) {
        return $dir;
    } else {
        _mkdir(dirname($dir), $mode);//循环调用创建多级目录
        @mkdir($dir, $mode);
        return $dir;
    }
}

/*
加载并实例化类
*/
function _instance($file, $args = array(), $fileDir = '')
{//传递参数为数组
    static $isNew = array();
    $arr = explode('/', trim($file));//切割为数组
    $className = $arr [count($arr) - 1];//获取类名
    _import($file . '.class.php', $fileDir);
    if (isset($isNew[$className])) {
        return $isNew[$className];//避免重复实例化同一个类
    } else {
        if (class_exists($className)) {
            $isNew[$className] = new $className($args);
            return $isNew[$className];
        } else {
            _error('classError', '文件名应与类名一致并且文件后缀应以.class.php结尾：' . $className . ' 类不存在', true);
        }
    }

}

/*
加载文件
*/
function _import($file, $fileDir = '')
{
    static $isLoad = array();
    if ($fileDir == 1) {
        $importFile = FRAMK . str_replace('/', S, $file);
    } else {
        $importFile = APP_ROOT . str_replace('/', S, $file);
    }

    if (file_exists($importFile) && is_readable($importFile)) {
        if (isset($isLoad[$importFile])) {
            return true;//避免重复加载
        } else {
            require($importFile);
            $isLoad[$importFile] = true;
            return true;
        }
    } else {
        _error('fileNotExist', '请检查此目录下文件是否存在:' . $importFile, true);
    }

}

/* 
获取Exception.php下配置数组键名相对应的值，$detail为错误详情	
*/
function _error($errorKey, $detail = '', $exit = false)
{

    if ($GLOBALS['Debug'] == true) {    //如果Debu为真则显示详细，否则直接显示“访问出错”跳回或到主页，
        $errorArray = require(FRAMK . '_Error.php');
        foreach ($errorArray as $key => $value) {
            if (trim($key) == trim($errorKey)) {
                echo
                    '<div style="border:solid 1px #ccc;padding:5px;background-color:#eee;color:brown;font-size:12px;">!' . $value . ' : ' . $detail . '</div>';
                if ($exit) exit();
            }
        }
    } else {
        echo
            '<div style="border:solid 1px #ccc;padding:5px;background-color:#eee;color:brown;font-size:12px;">
			<meta http-equiv="Refresh" content=3;URL=' . ACT . '>
			!访问出错
			</div>';//可以自己设定
        exit();
    }
}

function msubstr($str, $start, $len)
{
    $tmpstr = "";
    $strlen = $start + $len;
    for ($i = 0; $i < $strlen; $i++) {
        if (ord(substr($str, $i, 1)) > 0xa0) {
            $tmpstr .= substr($str, $i, 2);
            $i++;
        } else
            $tmpstr .= substr($str, $i, 1);
    }
    return $tmpstr;
}

function utf_substr($str, $len)
{
    for ($i = 0; $i < $len; $i++) {
        $temp_str = substr($str, 0, 1);
        if (ord($temp_str) > 127) {
            $i++;
            if ($i < $len) {
                $new_str[] = substr($str, 0, 3);
                $str = substr($str, 3);
            }
        } else {
            $new_str[] = substr($str, 0, 1);
            $str = substr($str, 1);
        }
    }
    return join($new_str);
}

/*PHP 5.5新增array_column()数组函数,如果需要在低版本的PHP环境中使用，是不行的。
    本文介绍如何实现兼容低于PHP 5.5版本的array_column()函数
*/
if (!function_exists("array_column")) {
    function array_column($input, $columnKey, $indexKey = NULL)
    {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
        $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
        $result = array();

        foreach ((array)$input AS $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : NULL;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }
}

if (!function_exists("list2tree")) {

    /**r把列表数据转为树形
     * @param $list
     * @param int $pId
     * @param int $level
     * @param string $pk
     * @param string $pidk
     * @param string $name
     * @return array|string
     * Author: lingqifei created by at 2020/4/1 0001
     */
    function list2tree($list, $pId = 0, $level = 0, $pk = 'id', $pidk = 'pid', $name = 'name')
    {
        $tree = '';
        foreach ($list as $k => $v) {
            if ($v[$pidk] == $pId) { //父亲找到儿子
                $v['nodes'] = list2tree($list, $v[$pk], $level + 1, $pk, $pidk, $name);
                $v['level'] = $level + 1;
                $v['treename'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '|--' . $v[$name];
                $v['tags'] = $v['id'];
                $v['text'] = $v[$name];
                $tree[] = $v;
            }
        }
        return $tree;
    }
}

if (!function_exists("list2select")) {

    /**r把列表数据转为树形下拉
     * @param $list
     * @param int $pId
     * @param int $level
     * @param string $pk
     * @param string $pidk
     * @param string $name
     * @return array|string
     * Author: lingqifei created by at 2020/4/1 0001
     */
    function list2select($list, $pId = 0, $level = 0, $pk = 'id', $pidk = 'pid', $name = 'name',$data=array())
    {
        foreach ($list as $k => $v) {
            $v['treename'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '|--' . $v[$name];
            if ($v[$pidk] == $pId) { //父亲找到儿子
                $data[] =$v;
                $data   = list2select($list, $v[$pk], $level + 1, $pk, $pidk, $name,$data);
            }
        }
        return $data;
    }
}

if (!function_exists("array2string")) {

    /**数组 ，把关键字和值转为数组
     * @param $array
     * @return string
     * Author: lingqifei created by at 2020/4/4 0004
     */
    function array2string($array){

        $string = [];

        if($array && is_array($array)){

            foreach ($array as $key=> $value){
                $string[] = $key.'->'.$value;
            }
        }

        return implode(',',$string);
    }
}
if (!function_exists("date_range")) {

    /**数组 ，把关键字和值转为数组
     * @param $array
     * @return string
     * Author: lingqifei created by at 2020/4/4 0004
     */
    //时间计算
    function date_range($range,$format='Y-m-d'){
        $date_range=date($format,strtotime($range,time()));
        return $date_range;
    }
}

if (!function_exists("export_to_cvs")) {
    /**
     * @data 2020/01/05
     * @desc 数据导出到excel(csv文件)
     * @param $filename 导出的csv文件名称 如date("Y年m月j日").'-test.csv'
     * @param array $tileArray 所有列名称
     * @param array $dataArray 所有列数据
     */
    function export_to_cvs($filename, $tileArray = array(), $dataArray = array())
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);
        ob_end_clean();
        ob_start();
        header("Content-Type: text/csv");
        header("Content-Disposition:filename=" . $filename);
        $fp = fopen('php://output', 'w');
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        fputcsv($fp, $tileArray);
        $index = 0;
        foreach ($dataArray as $item) {
            if ($index == 1000) {
                $index = 0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp, $item);
        }
        ob_flush();
        flush();
        ob_end_clean();
    }
}
/*  +------------------------------------------------------------------------------ */

if (!function_exists('download')) {

    /**
     * 文件下载函数
     * Author: lingqifei created by at 2020/6/4 0004
     */
    function download($filepath,$filename='downfile.zip')
    {
        // 检查文件是否存在
        if (!file_exists($filepath)) {
            $this->error('文件未找到');
        } else {
            // 打开文件
            $file1 = fopen($filepath, "r");
            // 输入文件标签
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length:" . filesize($filepath));
            Header("Content-Disposition: attachment;filename=" . $filename);
            ob_clean();     // 重点！！！
            flush();        // 重点！！！！可以清除文件中多余的路径名以及解决乱码的问题：
            //输出文件内容
            //读取文件内容并直接输出到浏览器
            echo fread($file1, filesize($filepath));
            fclose($file1);
            exit();
        }
    }
}

?>