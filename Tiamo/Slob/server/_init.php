<?php
/**
 * Created by PhpStorm.
 * User: xiangdong
 * Date: 15/11/20
 * Time: 上午10:19
 */
define('DEBUG', 'on');
define('WEBPATH', __DIR__);
define('SWOOLE_SERVER', true);
//框架引入
require '/data/www/public/framework/libs/lib_config.php';
//生命全局$php 并将swoole 赋值给$php
global $php;
$php = Swoole::getInstance();
Swoole\Error::$stop = false;
Swoole\Error::$echo_html = false;
Swoole\Error::$display = false;
Swoole\Loader::addNameSpace('Server', __DIR__ . '/classes');
$env = get_cfg_var('env.name');
if ($env == 'dev' or $env == 'test' or $env == 'local')
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/dev');
}
else
{
    Swoole::$php->config->setPath(WEBPATH . '/apps/configs/product');
}