<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Amqp;
use Tang\Exception\IsNotCallableException;

class Queue
{
    /**
     * AMQPQueue对象
     * @var AMQPQueue
     */
    protected $queue;
    public function __construct(Exchannel $exchannel,$name,$config)
    {
        $this->queue = new \AMQPQueue($exchannel->getChannel());
        $this->queue->setName($name);
        $config = array_replace_recursive(['durable'=>false,'passive'=>false,'autoDelete'=>false,'internal'=>false,'noWait'=>false,'routingKey'=>'','arguments'=>[]],$config);
        $flags = 0;
        if($config['durable'])
        {
            $flags = $flags | AMQP_DURABLE;
        }
        if($config['passive'])
        {
            $flags = $flags | AMQP_PASSIVE;
        }
        if($config['autoDelete'])
        {
            $flags = $flags | AMQP_AUTODELETE;
        }
        if($config['internal'])
        {
            $flags = $flags | AMQP_INTERNAL;
        }
        if($config['noWait'])
        {
            $flags = $flags | AMQP_NOWAIT;
        }
        $this->queue->setFlags($flags);
        if(isset($config['arguments']) && is_array($config['arguments']))
        {
            $this->queue->setArguments($config['arguments']);
        }
        $this->queue->declare();
        $this->queue->bind($exchannel->getName(),$config['routingKey']);
    }

    /**
     * 非阻塞获取消息
     * 有消息会传递Message对象到 $callback
     * $callback
     * @param $callback
     * @param bool $isAutoAck
     * @throws IsNotCallableException
     */
    public function get($callback,$isAutoAck = false)
    {
        if(!is_callable($callback))
        {
            throw new IsNotCallableException();
        }
        $envelope = $this->queue->get($isAutoAck ? AMQP_AUTOACK:AMQP_NOPARAM);
        if($envelope)
        {
            $message = Message::create($envelope,$this);
        }
        $callback($message);
    }

    /**
     * 阻塞获取消息
     * 有消息会传递Message对象到 $callback
     * 当$callback返回false的时候，则会停止获取消息
     * @param $callback
     * @param bool $isAutoAck
     * @throws IsNotCallableException
     */
    public function consume($callback,$isAutoAck = false)
    {
        if(!is_callable($callback))
        {
            throw new IsNotCallableException();
        }
        $flags = $isAutoAck ? AMQP_AUTOACK:AMQP_NOPARAM;
        $running = true;
        $callback2 = function($envelope,$queue) use($callback,$running)
        {
            $message = Message::create($envelope,$this);
            $result = $callback($message);
            if($result === false)
            {
                $running = false;
            }
        };
        while(true)
        {
            if($running)
            {
                break;
            }
            $this->queue->consume($callback2,$flags);
        }
    }
    /**
     * 清除所有暂存在Queue里面的消息
     * @return mixed
     */
    public function purge()
    {
        return $this->queue->purge();
    }
    public function __call($method,$arguments)
    {
        call_user_func([$this->queue,$method],$arguments);
    }
}