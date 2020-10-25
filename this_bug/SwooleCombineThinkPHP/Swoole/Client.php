<?php

class Client
{
    protected $client;

    public function __construct( $sock_type,  $is_sync)
    {
        if ($sock_type == false || $is_sync == false) {
            $sock_type = SWOOLE_SOCK_TCP;
            $is_sync   = SWOOLE_SOCK_ASYNC;
        }

        $this->client = new swoole_client($sock_type, $is_sync);
        $this->client->on("connect", function($cli) {
            // 连接回调函数，客户端连接成功之后回调此处
        });
        $this->client->on("receive", function($cli, $data){
            // 客户端收到数据之后回调次数，收到的数据存放在$data变量中
        });
        $this->client->on("error", function($cli){
            // 连接失败回调，客户端连接失败后回调此处
        });
        $this->client->on("close", function($cli){
            // 连接断开回调，连接被服务器断开之后回调此处
        });
    }

    /**
     * 异步投递数据
     *
     * @param $data
     */
    public function send($data)
    {
        $this->client->on("connect", function (swoole_client $cli) use ($data) {
            $cli->send($data);
        });
        $this->client->connect('0.0.0.0', 9501);
    }
}