<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 21:01
 */

namespace fastwork\swoole;


class WsHttpServer extends HttpServer
{


    public function onOpen(\swoole_server $server, \swoole_http_request $request)
    {
    }

    public function onClose(\swoole_server $server, $fd, $reactor_id)
    {

    }

    /**
     * Ws回调消息
     * @param $server
     * @param $frame
     */
    public function onMessage($server, $frame)
    {

    }
}