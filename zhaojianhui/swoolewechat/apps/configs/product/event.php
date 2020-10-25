<?php
$event['master'] = [
    'id'  => 'event', //redis数据库配置
    'key' => 'eventList', //事件redis key
    //'type' => Swoole\Queue\Redis::class,//队列类
    'type'  => App\Queue\RabbitMQ::class, //rabbitmq队列类
    'async' => true, //是否异步
];

return $event;
