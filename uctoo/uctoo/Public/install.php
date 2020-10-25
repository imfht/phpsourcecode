<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
header("Content-Type:text/html;charset=utf-8");
function reset_session_path()
{
    $root = str_replace("\\", '/', dirname(__FILE__));
    $savePath = $root . "/tmp/";
    if (!file_exists($savePath))
        @mkdir($savePath, 0777);
    session_save_path($savePath);
}

//reset_session_path();  //如果您的服务器无法安装或者无法登陆，又或者后台验证码无限错误，请尝试取消本行起始两条左斜杠，让本行代码生效，以修改session存储的路径

if (version_compare(PHP_VERSION, '5.4.0', '<'))
    die('当前PHP版本' . PHP_VERSION . '，最低要求PHP版本5.4.0 <br/><br/>很遗憾，未能达到最低要求。本系统必须运行在PHP5.4 及以上版本。如果您是虚拟主机，请联系空间商升级PHP版本，如果您是VPS用户，请自行升级php版本或者联系VPS提供商寻求技术支持。');

define('BIND_MODULE', 'install');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ('APP_DEBUG', true);

/**
 * 应用目录设置
 * 安全起见，建议安装调试完成后移动到非WEB目录
 */

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ('RUNTIME_PATH', __DIR__ .'/../runtime/');


// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';
