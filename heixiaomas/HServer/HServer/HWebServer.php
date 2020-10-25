<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/2/20
 * Time: 15:20
 */

namespace HServer;

require_once __DIR__ . '/../vendor/wokerman/workerman/Autoloader.php';

use HServer\core\view\Dispatcher;
use HServer\core\http\Link;
use HServer\core\http\Request;
use HServer\core\http\Response;
use Workerman\Worker;
use HServer\core\file\StaticFiles;

class HWebServer extends Worker
{
    public function __construct($socket_name = '', array $context_option = array())
    {
        parent::__construct($socket_name, $context_option);
    }

    public function onClientMessage($connection, $data)
    {

        /*
         * 根据业务自己调整
         */
        static $request_count;
        if (++$request_count > 10000) {
            // 请求数达到10000后退出当前进程，主进程会自动重启一个新的进程
            parent::stopAll();
        }

        /**
         * 构造Req，resp
         */
        $req = new  Request($data);
        $resp = new Response($connection);

        /**
         * 检查静态文件，是否存在
         */
        $temp = new StaticFiles($connection);
        if ($temp->invoke()) {
            return false;
        }
        /**
         * 检查Filter是否存在
         */

        Link::invoke($req, $resp);

        /**
         * 检查控制器是否存在,进行分发
         */
        Dispatcher::display($resp, $req);
    }

    public function run()
    {
        Worker::$logFile = __DIR__ . '/../log/web.log';
        Worker::$stdoutFile = __DIR__ . '/../log/web_stdout.log';
        $this->reusePort = true;
        $this->onMessage = array($this, 'onClientMessage');
        parent::run();
    }

}