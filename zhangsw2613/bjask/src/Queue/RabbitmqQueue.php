<?php
/**
 * rabbitmq队列类
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 13:21
 */

namespace Bjask\Queue;

use Enqueue\AmqpExt\AmqpConnectionFactory;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\Impl\AmqpBind;

class RabbitmqQueue implements QueueHandlerInterface
{

    private $factory = null;
    private $context = null;
    private $topic = null;
    private $myQueue = null;
    private $messageCount = 0;//未处理的消息数
    private $config = [];

    public function __construct(array $config)
    {
        $this->config['host'] = $config['host'];
        $this->config['port'] = $config['port'];
        $this->config['user'] = $config['user'];
        $this->config['pass'] = $config['pass'];
        $this->config['vhost'] = $config['vhost'];
    }

    /**
     * 创建连接
     * @param string $topic_name
     * @return $this
     */
    public function createConnection(string $topic_name)
    {
        $this->factory = new AmqpConnectionFactory($this->config);
        $this->context = $this->factory->createContext();
        $this->topic = $this->context->createTopic($topic_name . '.topic');
        $this->topic->addFlag(AmqpTopic::FLAG_DURABLE);
        $this->topic->setType(AmqpTopic::TYPE_FANOUT);
        //$this->context->deleteTopic($this->topic);
        $this->context->declareTopic($this->topic);
        $this->myQueue = $this->context->createQueue($topic_name . '.queue');
        $this->myQueue->addFlag(AmqpQueue::FLAG_DURABLE);
        $this->messageCount = $this->context->declareQueue($this->myQueue);
        $this->context->bind(new AmqpBind($this->topic, $this->myQueue));//把队列绑定到该交换机
        return $this;
    }

    /**
     * 发送一个消息到队列
     * @param string $message
     * @return mixed
     */
    public function push(string $message)
    {
        $message = $this->context->createMessage($message);
        return $this->context->createProducer()->send($this->myQueue, $message);
    }

    /**
     * 取出一个消息
     * @return mixed
     */
    public function pop()
    {
        $consumer = $this->context->createConsumer($this->myQueue);
        if ($m = $consumer->receive(1)) {
            $consumer->acknowledge($m);
            return $m->getBody();
        }
        return null;
    }

    /**
     * 返回当前队列未处理的消息数
     * @return int
     */
    public function len()
    {
        return $this->messageCount;
    }

    /**
     * 返回当前连接状态
     * @return mixed
     */
    public function isConnected()
    {
        return $this->context->getExtChannel()->getConnection()->isConnected();
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        $this->context->close();
    }
}