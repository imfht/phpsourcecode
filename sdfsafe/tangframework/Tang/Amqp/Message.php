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
/**
 * Amqp消息封装
 * Class Message
 * @package Tang\Amqp
 */
class Message
{
    /**
     * @var
     */
    protected $msg;
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * 确认已处理消息，当$multiple为true的时候，发送
     * @param bool $multiple
     * @return mixed
     */
    public function ack($multiple = false)
    {
        return $this->queue->ack($this->msg->getDeliveryTag(),$multiple ? AMQP_MULTIPLE:AMQP_NOPARAM);
    }

    /**
     * 没有处理消息
     * @param bool $multiple
     * @param bool $requeue 为true的话则会将消息发送到下一个消费者。否则删除消息
     * @return mixed
     */
    public function nack($multiple = false,$requeue = false)
    {
        $flags = AMQP_NOPARAM;
        if($multiple && $requeue)
        {
            $flags = AMQP_MULTIPLE ^ AMQP_REQUEUE;
        } else if($multiple)
        {
            $flags = AMQP_MULTIPLE;
        } else{
            $flags = AMQP_REQUEUE;
        }
        return $this->queue->ack($this->msg->getDeliveryTag(),$flags);
    }
    /**
     * 拒绝本条消息 $requeue为true的话则会将消息发送到下一个消费者。否则删除消息
     * @param bool $requeue
     * @return mixed
     */
    public function reject($requeue = false)
    {
        return $this->queue->reject($this->msg->getDeliveryTag(),$requeue?AMQP_REQUEUE:AMQP_NOPARAM);
    }
    /**
     * 获取消息主体
     * @return string
     */
    public function body()
    {
        return $this->msg->getBody();
    }

    /**
     * 返回队列
     * @return Queue
     */
    public function queue()
    {
        return $this->queue;
    }
    /**
     * 调用envelope的方法
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method,$args)
    {
        return call_user_func_array([$this->msg,$method],$args);
    }
    /**
     * 创建Message
     * @param $msg
     * @return Message
     */
    public static function create($msg,Queue $queue)
    {
        $instance = new Message();
        $instance->msg = $msg;
        $instance->queue = $queue;
        return $instance;
    }
}