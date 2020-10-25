<?php

/**
 * 客户端请求
 *
 * @author: moxiaobai
 * @since : 2015/5/15 14:11
 */


$client = new swoole_client(SWOOLE_SOCK_TCP);

$client->on("receive", function($cli, $data){
    echo "Received: ".$data."\n";
});

//发起网络连接
$ret = $client->connect('127.0.0.1', 9503, 0.5);
if(!$ret) {
    echo "Over flow. errno=". $client->errCode;
}


//添加数据
$data = array(
    's_id'       => 4,
    's_interval' => 3000,
    's_title'    => '我去我去',
    's_url'      => 'http://queue.caihong.com/stat/stat/login/',
    'u_id'       => 1
);
//$buffer = array('type'=>'add', 'list'=>$data);

//删除数据
$delData = array('s_id'=>3, 's_timerId'=>3);
$buffer  = array('type'=>'del', 'list'=>$delData);
$buffer  = json_encode($buffer) . "\r\n\r\n";

$client->send($buffer);


$ret =  $client->recv();
print_r(json_decode($ret, true));


