<?php
//载入初始化文件
require_once __DIR__ . '/initServer.php';

//添加自定义路由,第二个参数表示模块是否插入到开头
Swoole::getInstance()->addRouter(new App\Router\ModuleRouter(), true);
Swoole\Config::$debug = false;
//设置PID文件的存储路径
Swoole\Network\Server::setPidFile(WEBPATH . '/server/pid/appServer.pid');

/**
 * 显示Usage界面
 * php app_server.php start|stop|reload
 */
Swoole\Network\Server::start(function () {
    $server = Swoole\Protocol\WebServer::create(WEBPATH . '/server/swoole.ini');
    //设置应用所在的目录
    $server->setAppPath(WEBPATH . '/apps/');
    //设置应用所在的目录
    $server->setDocumentRoot(WEBPATH);
    //Logger
    $server->setLogger(new \Swoole\Log\EchoLog(WEBPATH . "/logs/webserver.logs"));
    //作为守护进程
    //$server->daemonize();
    //启动
    $server->run(['worker_num' => 1, 'max_request' => 5000, 'log_file' => WEBPATH.'/logs/app-swoole.logs']);
});
