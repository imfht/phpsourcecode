<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 9:47
 */
//版本检查
if (version_compare(PHP_VERSION, '5.3.0', '<')){exit('Sorry,naples require php5.4 or higher.<br/>很抱歉，naples_php需要php5.4或更高的版本。<br/><a href="https://coding.net/u/yuri2/p/naples_php/git" target="_blank">项目地址</a>');}

//安装环境检测
if (!is_file(__DIR__ . '/install/lock')) {
    require __DIR__ . '/install/envCheck.php';
    exit();
}


//计时开始
$mtime=explode(' ',microtime());
define('CORE_RUN_AT',$mtime[1]+$mtime[0]);

//常量定义
define('TIMESTAMP',time());//时间戳 注意；该时间戳精确到秒，且不是实时的
define('PATH_NAPLES',__DIR__); //naples核心目录
define('PATH_APP',PATH_NAPLES.'/app'); //app目录
define('PATH_DATA',PATH_NAPLES.'/data'); //data目录
define('PATH_RUNTIME',PATH_NAPLES.'/runtime'); //runtime目录
define('PATH_ROOT',dirname(PATH_NAPLES)); //根目录
define('PATH_EXTEND',PATH_NAPLES.'/extend'); //extend扩展目录
define('PATH_VENDOR',PATH_NAPLES.'/vendor'); //vendor扩展目录
define('PATH_UE_UPLOAD',PATH_PUBLIC.'/html/ueditor/upload'); //ue上传目录
define('FLAG_NOT_SET',md5('hello,naples')); //一个不太会重复的字符串，可用于检查参数是否是默认值
define('NAPLES_ADMIN','d5fc4b0e45c8f9a333c0056492c191cf');//管理员密码md5 请勿直接修改此行
define('VAR_PREFIX','np_yuri2.');//session添加前缀，避免与其他网站混淆
define('DS','/');//分隔符
define('RN',"\r\n");//回车符
define('ID',md5(uniqid(md5(microtime(true)),true)));//本次访问的id,用于日志记录

//加载Autoload
require PATH_NAPLES.'/lib/Autoload.php';
\naples\AutoLoad::register();

/**
 * 定义tick函数
 * @param $title string 计时事项的描述
 * 两次同样的title调用表示title事项计时完成
 */
function tick($title){
    $debug=\naples\lib\Factory::getDebug();
    $debug->tick($title);
}

tick('核心流程');
$obj=\naples\lib\Factory::getCore(); //进入主流程
tick('核心流程');

