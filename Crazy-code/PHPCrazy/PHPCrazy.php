<?php
/*
*   PHPCrazy 框架初始化文件
*   
*   Package:        PHPCrazy
*   Link:           http://zhangyun.org/
*   Author:         Crazy <mailzhangyun@qq.com>
*   Copyright:      2014-2015 Crazy
*   License:        Please read the LICENSE file.
*/

if (!defined('IN_PHPCRAZY')) exit;

ob_start();

try {

    // 关闭魔术引号
    @set_magic_quotes_runtime(0);

    // 初始化程序目录
    define('ROOT_PATH', dirname(__FILE__));

    // 引用安装配置信息
    @include ROOT_PATH . '/#config.php';

    // 如果没有安装则跳转到安装流程
    if (!defined('INSTALL_FINISH')) {

        header('Location: install/install.php');
    }

    $PDO = new PDO(DSN, DB_USER, $DB_PASS);

    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (defined('DB_ENCODE')) {
        
        $PDO->exec('set names utf8');
    }

    // 注销数据库密码这个变量, 预防第三方插件非法调用
    unset($DB_PASS);

    // 加载常量
    require ROOT_PATH . '/includes/constants.php';

    $sql = 'SELECT config_name, config_value FROM ' . CONFIG_TABLE;

    $result = $PDO->query($sql);

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {

        $C[$row['config_name']] = $row['config_value'];

    }

    // 设置时区
    @date_default_timezone_set($C['timezone']);

    // 加载常用函数
    require_once ROOT_PATH . '/includes/lib/function.php';

    // 加载语言
    require Lang('main');

    // 自动加载类库
    function __autoload($classname) {

        AutoloadClass($classname);
    }

    // 初始化Session
    $S = new Session();

    // 初始化UserData
    $U = $S->Init();

} catch (Exception $e) {

    ob_end_clean();

    header('Content-type: text/html; charset=UTF-8');

    if (isset($PDO)) {
        
        $PDO = null;
    }

    die($e->getMessage().' 在 '.basename($e->getFile()).' 第 ' .$e->getLine() .' 行');
}

?>