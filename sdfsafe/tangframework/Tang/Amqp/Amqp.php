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
use Tang\Amqp\Interfaces\IAmqp;
use Tang\Exception\SystemException;
use Tang\Log\LogService;

class Amqp implements IAmqp
{
    /**
     * 交换机数组
     * @var array
     */
    private $exchannels = [];
    /**
     * 配置数组
     * @var array
     */
    private $config = [];
    /**
     * @var AMQPConnection
     */
    private $amqpConnection;
    public function __construct($config)
    {
        $this->config = $config;
        $this->createAmqpConnection();
    }

    public function createChannel()
    {
        return new \AMQPChannel($this->amqpConnection);
    }
    public function getExchannel($name)
    {
        if(!$this->existsExchannel($this->exchannels[$name]))
        {
            $this->createExchannelByConfig($name);
        }
        return $this->exchannels[$name];
    }
    public function existsExchannel($name)
    {
        return isset($this->exchannels[$name]);
    }
    public function creareExchannel($name,$type='direct',$durable = false,$passive = false,$autoDelete = false,$internal = false,$noWait = false,array $arguments=[])
    {
        if($this->existsExchannel($this->exchannels[$name]))
        {
            throw new SystemException('%s exchannel is exist',[$name],1504,'ERR');
        }
        return $this->createExchannelByArray($name,compact('type','durable','passive','autoDelete','internal','noWait','arguments'));
    }
    public function __destruct()
    {
        if($this->amqpConnection)
        {
            $this->amqpConnection->disconnect();
        }
    }
    protected function createExchannelByConfig($name)
    {
        if(!isset($this->config['exchannels'][$name]))
        {
            throw new SystemException('%s exchannel configuration does not exist',[$name],1507,'ERR');
        }
        return $this->createExchannelByArray($name,$this->config['exchannels'][$name]);
    }
    protected function createExchannelByArray($name,$config)
    {
        $exchannel = new Exchannel($this,$name,$config);
        $this->exchannels[$name] = $exchannel;
        return $exchannel;
    }
    protected function createAmqpConnection()
    {
        $servers = $this->config['servers'];
        while(true)
        {
            if(!$servers)
            {
                throw new SystemException('amqp servers is empty',null,1508,'ERR');
            }
            $index = array_rand($servers);
            $host = $servers[$index];
            if($host['host'])
            {
                $connection = new \AMQPConnection($host);
                if(!$connection->connect())
                {
                    LogService::write('amqp server :'.$host['host'].' connect faild!','ERR');
                } else
                {
                    $this->amqpConnection = $connection;
                    break;
                }
            }
            unset($servers[$index]);
        }

    }
}