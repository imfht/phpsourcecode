<?php

namespace App\Queue;

/**
 * Class RabbitMQ.
 */
class RabbitMQ implements \Swoole\IFace\Queue
{
    //队列主配置
    protected $rabbitFactoryKey;
    protected $exchangeName = 'swoole';
    protected $queueName    = 'swoole:queue';

    public function __construct($config)
    {
        if (empty($config['id'])) {
            $config['id'] = 'master';
        }
        $this->rabbitFactoryKey = $config['id'];
        if (!empty($config['key'])) {
            $this->queueName = $config['key'];
        }

        \Swoole::$php->rabbitmq->setExchangeName($this->rabbitFactoryKey);
        \Swoole::$php->rabbitmq->setQueueName($this->queueName);
    }

    public function __debugInfo()
    {
        // TODO: Implement __debugInfo() method.
    }

    /**
     * 出队
     */
    public function pop()
    {
        $ret = \Swoole::$php->rabbitmq->pop($this->queueName);
        if ($ret) {
            return unserialize($ret);
        }

        return false;
    }

    /**
     * 入队
     *
     * @param $data
     */
    public function push($data)
    {
        return \Swoole::$php->rabbitmq->push(serialize($data));
    }
}
