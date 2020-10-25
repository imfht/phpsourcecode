<?php
/*echo serialize(['type'=>'hello','data'=>['name'=>'赵建辉','time'=>time()]]);
exit();*/
/**
 * 配置文件：apps/configs/event.php
 * 事件列表：apps/events/*.php
 * 具体事件处理代码：apps/classes/Handler/*.php
 */
//初始化文件
define('DEBUG', 'on');
define('DS', DIRECTORY_SEPARATOR);
//设置当前web目录
define('WEBPATH', __DIR__ . DS . '..');
//设置APP目录
define('APPSPATH', WEBPATH .'/apps');
//使用composer扩展
require_once WEBPATH . '/vendor/autoload.php';
//载入swoole frameworkZ框架配置
require_once WEBPATH . '/vendor/matyhtf/swoole_framework/libs/lib_config.php';
//执行脚本
Swoole\Config::$debug = true;
for ($i=0; $i<10; $i++){
    $res = Swoole::$php->event->trigger("hello2", array($i, "打打打死你", __DIR__));
}
