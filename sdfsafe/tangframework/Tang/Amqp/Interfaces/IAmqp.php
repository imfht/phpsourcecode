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
namespace Tang\Amqp\Interfaces;
use Tang\Interfaces\ISetConfig;

interface IAmqp
{
    /**
     * 创建Channel
     * @return \AMQPChannel
     */
    public function createChannel();

    /**
     * 根据名字获取Exchannel
     * @return \Tang\Amqp\Exchannel
     */
    public function getExchannel($name);

    /**
     * 判断$name Exchannel是否存在
     * @param $name
     * @return true
     */
    public function existsExchannel($name);

    /**
     * @param $name
     * @param string $type
     * @param bool $durable
     * @param bool $passive
     * @param bool $autoDelete
     * @param bool $internal
     * @param bool $noWait
     * @param array $arguments
     * @throw SystemException 如果$name的exchannel存在
     * @return \Tang\Amqp\Exchannel
     */
    public function creareExchannel($name,$type='direct',$durable = false,$passive = false,$autoDelete = false,$internal = false,$noWait = false,array $arguments=[]);
}