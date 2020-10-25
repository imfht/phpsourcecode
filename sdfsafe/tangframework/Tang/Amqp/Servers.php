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
use Tang\Exception\SystemException;

class Servers
{
    protected $config = [];
    protected $amqps = [];
    public function setConfig($config)
    {
        $this->config = $config;
    }
    public function get($name = '')
    {
        if(!$name)
        {
            $name = reset(array_keys($this->config));
        }
        if(!isset($this->amqps[$name]))
        {
            $this->createAmqp($name);
        }
        return $this->amqps[$name];
    }
    protected function createAmqp($name)
    {
        if(!isset($this->config[$name]))
        {
            throw new SystemException('not found amqp %s config',[$name],3698);
        }
        $this->amqps[$name] = new Amqp($this->config[$name]);
    }
}