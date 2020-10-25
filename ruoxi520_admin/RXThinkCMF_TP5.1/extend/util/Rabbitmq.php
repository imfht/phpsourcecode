<?php
// +----------------------------------------------------------------------
// | RXThinkCMF框架 [ RXThinkCMF ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2020 南京RXThinkCMF研发中心
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <1175401194@qq.com>
// +----------------------------------------------------------------------

namespace util;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * 消息队列 公共类库
 * @author 牧羊人
 * @date 2019/11/25
 * Class Rabbitmq
 * @package util
 */
class Rabbitmq
{
    // 链接
    protected $connection;
    // 信道
    protected $channel;
    // 参数
    protected $priority;
    // 队列名称
    protected $queueName;
    // 交换机名称
    protected $exchangeName;
    // 路由关键字(可省略)
    protected $routingKey;
    // 交换机类型
    protected $exchangeType;
    // MQ默认链接配置
    protected $config = array(
        'host' => '127.0.0.1',
        'port' => '5672',
        'user' => 'guest',
        'password' => 'guest',
        'vhost' => '/',
    );

    /**
     * 构造方法
     * Rabbitmq constructor.
     * @param $queueName
     * @param null $priority
     */
    public function __construct($queueName, $priority = null)
    {
        // 队列名称
        $this->queueName = 'queue_' . $queueName;
        // 交换机名称
        $this->exchangeName = 'exchange_' . $queueName;
        // 路由关键字
        $this->routingKey = 'route_' . $queueName;
        // 交换机类型
        $this->exchangeType = 'topic';
        //
        $this->priority = $priority;
        // 创建链接与信道
        $this->createConnect();
        // 返回连接
        return $this->connection;
    }

    /**
     * 创建链接与信道
     * @author zongjl
     * @date 2019/7/4
     */
    protected function createConnect()
    {
        $host = $this->config['host'];
        $port = $this->config['port'];
        $user = $this->config['user'];
        $password = $this->config['password'];
        $vhost = $this->config['vhost'];
        if (empty($host) || empty($port) || empty($user) || empty($password)) {
            throw new Exception('RabbitMQ的连接配置不正确');
        }
        // 创建链接
        $this->connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        // 创建信道
        $this->channel = $this->connection->channel();
        // 绑定交换机
        $this->createExchange();
    }

    /**
     * 创建并绑定交换机
     * @return mixed
     * @author zongjl
     * @date 2019/7/4
     */
    protected function createExchange()
    {
        // 初始化交换机
        $this->channel->exchange_declare($this->exchangeName, $this->exchangeType, false, true, false);
        // 初始化一条队列
        if (!empty($this->priority)) {
            $priorityArr = array('x-max-priority' => array('I', $this->priority));
            $size = $this->channel->queue_declare($this->queueName, false, true, false, false, false, $priorityArr);
        } else {
            $size = $this->channel->queue_declare($this->queueName, false, true, false, false);
        }
        // 将队列与某个交换机进行绑定，并使用路由关键字
        $this->channel->queue_bind($this->queueName, $this->exchangeName, $this->routingKey);
        return $size;
    }

    /**
     * 发送消息到队列
     * @param $data
     * @author zongjl
     * @date 2019/7/4
     */
    public function send($data)
    {
        // 消息主体
        $body = json_encode($data);
        //创建消息$msg = new AMQPMessage($data,$properties)
        //#$data  string类型 要发送的消息
        //#roperties array类型 设置的属性，比如设置该消息持久化[‘delivery_mode’=>2]
        $msg = new AMQPMessage($body, array('content_type' => 'application/json', 'delivery_mode' => 2));
        // 推送消息到某个交换机
        $this->channel->basic_publish($msg, $this->exchangeName, $this->routingKey);
    }

    /**
     * 接收消息队列
     * @return array
     * @author zongjl
     * @date 2019/7/4
     */
    public function receive()
    {
        // 信道
        $channel = $this->channel;
        // 获取消息
        $message = $channel->basic_get($this->queueName);
        if (!$message) {
            return array(null, null);
        }
        $ack = function () use ($channel, $message) {
            $channel->basic_ack($message->delivery_info['delivery_tag']);
        };
        // 消息主体
        $result = json_decode($message->body, true);
        return array($ack, $result);
    }

    /**
     * 关闭连接
     * @author zongjl
     * @date 2019/7/4
     */
    public function close()
    {
        // 关闭信道
        $this->channel->close();
        // 关闭连接
        $this->connection->close();
    }

    /**
     * 获得队列长度
     * @return int
     */
    public function length()
    {
        $info = $this->bindExchange();
        return $info[1];
    }
}
