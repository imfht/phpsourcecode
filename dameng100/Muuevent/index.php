<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
//MuuCmf入口文件
function reset_session_path()
{
    $root = str_replace("\\", '/', dirname(__FILE__));
    $savePath = $root . "/tmp/";
    if (!file_exists($savePath))
       @mkdir($savePath, 0777);
    session_save_path($savePath);
}

//reset_session_path();  //如果您的服务器无法安装或者无法登陆，又或者后台验证码无限错误，请尝试取消本行起始两条左斜杠，让本行代码生效，以修改session存储的路径


if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

/*移除magic_quotes_gpc参数影响*/
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
/*移除magic_quotes_gpc参数影响end*/

/*如果需要二级域名同步登录，取消双斜杠，将域名更换为你的主域名*/
//ini_set('session.cookie_domain','muucmf.cn');
/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ('APP_DEBUG', true);
define ('APP_PATH', './Application/');
/**
 *  主题目录 MUUCMF模板路径
 */
define ('MUUCMF_THEME_PATH', './Theme/');

if (!is_file( 'Conf/user.php')) {
    header('Location: ./install.php');
    exit;
}

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ('RUNTIME_PATH', './Runtime/');

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
try{
    require './ThinkPHP/ThinkPHP.php';
}catch (\Exception $exception){
    if($exception->getCode()==815){
        send_http_status(404);
        $string=file_get_contents('./404.html');
        $string=str_replace('$ERROR_MESSAGE',$exception->getMessage(),$string);
        $string=str_replace('HTTP_HOST','http://'.$_SERVER['HTTP_HOST'],$string);
        echo $string;
    }else{
        E($exception->getMessage(),$exception->getCode());
    }
}
